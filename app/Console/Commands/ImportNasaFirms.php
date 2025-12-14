<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\Fire\FocosCalorService;
use Carbon\Carbon;

class ImportNasaFirms extends Command
{
    protected $signature = 'import:nasa-firms {days=2 : Number of days to import}';

    protected $description = 'Obtener datos de focos de calor desde APIs externas (NO guarda en BD)';

    // Integration API configuration
    private function getIntegrationApiUrl(): ?string
    {
        $url = config('services.hotspots_integration.api_url');
        return !empty($url) ? $url : null;
    }

    // NASA FIRMS API configuration - loaded from config/services.php
    private function getNasaApiKey(): string
    {
        $key = config('services.nasa_firms.api_key');
        if (empty($key)) {
            $this->error('NASA_FIRMS_API_KEY no está configurada en el archivo .env');
            $this->info('Por favor, agrega NASA_FIRMS_API_KEY=tu_api_key en tu archivo .env');
            exit(1);
        }
        return $key;
    }

    private function getNasaApiBase(): string
    {
        return config('services.nasa_firms.api_base', 'https://firms.modaps.eosdis.nasa.gov/api/area/csv');
    }
    
    // Bolivia bounding box
    private const BOLIVIA_BOUNDS = [
        'min_lat' => -22.9,
        'max_lat' => -9.7,
        'min_lng' => -69.6,
        'max_lng' => -57.5,
    ];

    public function handle()
    {
        $this->warn('NOTA: Este comando ya NO guarda datos en la base de datos.');
        $this->info('Los focos de calor se obtienen directamente de las APIs externas cuando se necesitan.');
        $this->line('');
        
        $days = $this->argument('days');
        
        // Usar el servicio que obtiene datos sin guardar en BD
        $service = app(FocosCalorService::class);
        $hotspots = $service->getRecentHotspotsWithFallback($days);
        
        $this->info("✓ Datos obtenidos: {$hotspots->count()} focos de calor");
        $this->info("✓ Estos datos NO se guardan en la base de datos");
        $this->info("✓ Se obtienen directamente de las APIs cuando se necesitan");
        
        return 0;
    }

    /**
     * Intentar obtener datos del endpoint de integración (2 intentos)
     */
    private function fetchFromIntegrationApi(): ?array
    {
        $apiUrl = $this->getIntegrationApiUrl();
        
        if (empty($apiUrl)) {
            $this->warn('HOTSPOTS_INTEGRATION_API_URL no está configurada, usando NASA FIRMS');
            return null;
        }

        $maxAttempts = 2;
        
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $this->info("Attempting to fetch from integration API (attempt {$attempt}/{$maxAttempts})...");
            
            try {
                $response = Http::timeout(30)->get($apiUrl);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                        $this->info("  ✓ Got " . count($data['data']) . " hotspots from integration API");
                        return $data['data'];
                    } else {
                        $this->warn("  - Invalid response format from integration API");
                    }
                } else {
                    $this->warn("  - HTTP error: " . $response->status());
                }
            } catch (\Exception $e) {
                $this->warn("  - Attempt {$attempt} failed: " . $e->getMessage());
            }
            
            // Esperar un poco antes del siguiente intento (excepto en el último)
            if ($attempt < $maxAttempts) {
                sleep(2);
            }
        }
        
        $this->warn("Failed to fetch from integration API after {$maxAttempts} attempts, using NASA FIRMS");
        return null;
    }

}

