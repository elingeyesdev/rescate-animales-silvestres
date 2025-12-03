<?php

namespace App\Http\Controllers;

use App\Models\AnimalHistory;
use App\Services\History\AnimalHistoryTimelineService;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class AnimalHistoryController extends Controller
{
	public function __construct(
		private readonly AnimalHistoryTimelineService $timelineService
	)
	{
		$this->middleware('auth');
        // Historial visible para cuidadores, rescatistas, veterinarios, encargados y administradores
        $this->middleware('role:cuidador|rescatista|veterinario|encargado|admin');
	}

	public function index(\Illuminate\Http\Request $request): View
    {
        $order = $request->get('order') === 'asc' ? 'asc' : 'desc';
        $histories = $this->timelineService->latestPerAnimalFileOrdered($order);

        return view('animal-history.index', compact('histories'))
            ->with('i', ($request->input('page', 1) - 1) * $histories->perPage());
    }

	public function show($id): View
	{
		// Intentar encontrar por ID de AnimalHistory primero
		$animalHistory = AnimalHistory::find($id);
		
		// Si no se encuentra, asumir que es un animal_file_id
		if (!$animalHistory) {
			$animalFileId = (int) $id;
			
			// Verificar que existe el AnimalFile
			$animalFile = \App\Models\AnimalFile::find($animalFileId);
			if (!$animalFile) {
				abort(404, 'Animal file not found');
			}
			
			// Buscar un historial existente para este animal_file_id
			$animalHistory = AnimalHistory::where('animal_file_id', $animalFileId)
				->orderByDesc('id')
				->first();

			// Si no existe, crear uno temporal solo para mostrar (no se guarda)
			if (!$animalHistory) {
				$animalHistory = new AnimalHistory();
				$animalHistory->animal_file_id = $animalFileId;
				$animalHistory->id = 0; // ID temporal
			}
		}

		$animalHistory->loadMissing(['animalFile.animal']);
        $animalFileId = $animalHistory->animal_file_id;
        
        $timeline = $animalFileId
            ? $this->timelineService->buildForAnimalFile($animalFileId)
            : [];
        $mapRoute = $animalFileId
            ? $this->timelineService->buildLocationRoute($animalFileId)
            : ['points' => []];

        return view('animal-history.show', [
            'animalHistory' => $animalHistory,
            'timeline' => $timeline,
            'mapRoute' => $mapRoute,
        ]);
	}

	public function pdf($animal_history): Response
	{
		// Intentar encontrar por ID de AnimalHistory primero
		$animalHistory = AnimalHistory::find($animal_history);
		
		// Si no se encuentra, asumir que es un animal_file_id
		if (!$animalHistory) {
			$animalFileId = (int) $animal_history;
		} else {
			$animalFileId = $animalHistory->animal_file_id;
		}
		
		// Verificar que existe el AnimalFile
		$animalFile = \App\Models\AnimalFile::with([
			'animal.report.condicionInicial',
			'animal.report.incidentType',
			'animal.report.firstTransfer.center',
			'animalStatus',
			'species',
			'center'
		])->findOrFail($animalFileId);
		
		// Obtener timeline y mapa
		$timeline = $this->timelineService->buildForAnimalFile($animalFileId);
		$mapRoute = $this->timelineService->buildLocationRoute($animalFileId);
		
		// Convertir imágenes WebP a formato compatible para el PDF
		$timeline = $this->convertWebPImages($timeline);
		$animalFileImage = $this->convertImageIfWebP($animalFile->imagen_url);
		
		// Preparar datos para el PDF
		$data = [
			'animalFile' => $animalFile,
			'animal' => $animalFile->animal,
			'report' => $animalFile->animal?->report,
			'timeline' => $timeline,
			'generatedAt' => now()->format('d/m/Y H:i:s'),
			'animalFileImagePath' => $animalFileImage,
		];
		
		// Generar PDF
		$pdf = Pdf::loadView('animal-history.pdf', $data);
		
		// Nombre del archivo
		$fileName = 'historial_' . ($animalFile->animal?->nombre ?? 'animal') . '_' . $animalFileId . '_' . date('d_m_Y') . '.pdf';
		
		return $pdf->download($fileName);
	}
	
	/**
	 * Convierte imágenes WebP a JPEG para compatibilidad con DomPDF
	 */
	private function convertWebPImages(array $timeline): array
	{
		foreach ($timeline as &$item) {
			if (!empty($item['image_url'])) {
				$item['image_url'] = $this->convertImageIfWebP($item['image_url']);
			}
		}
		return $timeline;
	}
	
	/**
	 * Convierte una imagen WebP a JPEG si es necesario, o retorna null si no se puede procesar
	 */
	private function convertImageIfWebP(?string $imagePath): ?string
	{
		if (empty($imagePath)) {
			return null;
		}
		
		$fullPath = storage_path('app/public/' . $imagePath);
		
		if (!file_exists($fullPath)) {
			return null;
		}
		
		// Si no es WebP, retornar la ruta original
		$extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
		if ($extension !== 'webp') {
			return $imagePath;
		}
		
		// Intentar convertir WebP a JPEG usando GD o Imagick
		try {
			// Verificar si GD está disponible y soporta WebP
			if (function_exists('imagecreatefromwebp')) {
				// Crear imagen desde WebP
				$image = @imagecreatefromwebp($fullPath);
				if ($image) {
					// Crear nombre para el JPEG temporal en el directorio temporal
					$tempDir = storage_path('app/temp');
					if (!is_dir($tempDir)) {
						mkdir($tempDir, 0755, true);
					}
					
					$jpegFileName = md5($imagePath) . '_' . time() . '.jpg';
					$jpegPath = $tempDir . '/' . $jpegFileName;
					
					// Convertir a JPEG
					if (imagejpeg($image, $jpegPath, 90)) {
						imagedestroy($image);
						// Retornar ruta relativa para el PDF
						return 'temp/' . $jpegFileName;
					}
					
					imagedestroy($image);
				}
			}
			
			// Si GD no funciona, intentar con Imagick si está disponible
			if (extension_loaded('imagick') && class_exists('Imagick')) {
				try {
					$imagick = new \Imagick($fullPath);
					$imagick->setImageFormat('jpeg');
					$imagick->setImageCompressionQuality(90);
					
					$tempDir = storage_path('app/temp');
					if (!is_dir($tempDir)) {
						mkdir($tempDir, 0755, true);
					}
					
					$jpegFileName = md5($imagePath) . '_' . time() . '.jpg';
					$jpegPath = $tempDir . '/' . $jpegFileName;
					
					if ($imagick->writeImage($jpegPath)) {
						$imagick->clear();
						$imagick->destroy();
						return 'temp/' . $jpegFileName;
					}
					
					$imagick->clear();
					$imagick->destroy();
				} catch (\Exception $e) {
					// Si Imagick falla, continuar
				}
			}
			
			// Si no se puede convertir, retornar null (no mostrar imagen)
			return null;
		} catch (\Exception $e) {
			// Si hay error, retornar null (no mostrar imagen)
			return null;
		}
	}
	
	/**
	 * Genera una imagen estática del mapa usando las coordenadas de los puntos
	 */
	private function generateMapImage(array $mapRoute): ?string
	{
		if (empty($mapRoute['points']) || count($mapRoute['points']) < 1) {
			return null;
		}
		
		$points = $mapRoute['points'];
		
		// Obtener todas las coordenadas válidas
		$coordinates = [];
		foreach ($points as $point) {
			if (!empty($point['lat']) && !empty($point['lon'])) {
				$lat = (float) $point['lat'];
				$lon = (float) $point['lon'];
				if (is_numeric($lat) && is_numeric($lon) && $lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180) {
					$coordinates[] = [
						'lat' => $lat,
						'lon' => $lon,
						'type' => $point['type'] ?? 'transfer',
					];
				}
			}
		}
		
		if (empty($coordinates)) {
			return null;
		}
		
		// Calcular el centro y los límites del mapa
		$lats = array_column($coordinates, 'lat');
		$lons = array_column($coordinates, 'lon');
		$centerLat = (min($lats) + max($lats)) / 2;
		$centerLon = (min($lons) + max($lons)) / 2;
		
		// Calcular el zoom aproximado basado en la distancia entre puntos
		$latDiff = max($lats) - min($lats);
		$lonDiff = max($lons) - min($lons);
		$maxDiff = max($latDiff, $lonDiff);
		
		$zoom = 13; // Zoom por defecto
		if ($maxDiff > 0.1) {
			$zoom = 10;
		} elseif ($maxDiff > 0.05) {
			$zoom = 11;
		} elseif ($maxDiff > 0.01) {
			$zoom = 12;
		} elseif ($maxDiff < 0.001) {
			$zoom = 15;
		}
		
		// Construir marcadores para la URL
		$markers = [];
		foreach ($coordinates as $coord) {
			$color = 'blue'; // azul por defecto (traslado)
			if ($coord['type'] === 'report') {
				$color = 'green';
			} elseif ($coord['type'] === 'release') {
				$color = 'orange';
			}
			
			$markers[] = $color . '|' . $coord['lat'] . ',' . $coord['lon'];
		}
		
		// Usar StaticMapAPI (servicio gratuito de mapas estáticos)
		// Formato: https://staticmap.openstreetmap.de/staticmap.php?center=LAT,LON&zoom=ZOOM&size=WIDTHxHEIGHT&markers=LAT1,LON1,COLOR1|LAT2,LON2,COLOR2
		$width = 800;
		$height = 600;
		$markerString = implode('|', array_slice($markers, 0, 20)); // Limitar a 20 marcadores
		
		// Construir URL del mapa estático
		$mapUrl = sprintf(
			'https://staticmap.openstreetmap.de/staticmap.php?center=%.6f,%.6f&zoom=%d&size=%dx%d&markers=%s',
			$centerLat,
			$centerLon,
			$zoom,
			$width,
			$height,
			urlencode($markerString)
		);
		
		// Descargar la imagen y guardarla temporalmente
		try {
			$imageContent = @file_get_contents($mapUrl);
			if ($imageContent === false) {
				return null;
			}
			
			$tempDir = storage_path('app/temp');
			if (!is_dir($tempDir)) {
				mkdir($tempDir, 0755, true);
			}
			
			$imageFileName = 'map_' . md5(serialize($coordinates)) . '_' . time() . '.png';
			$imagePath = $tempDir . '/' . $imageFileName;
			
			if (file_put_contents($imagePath, $imageContent) !== false) {
				return storage_path('app/temp/' . $imageFileName);
			}
			
			return null;
		} catch (\Exception $e) {
			return null;
		}
	}
}


