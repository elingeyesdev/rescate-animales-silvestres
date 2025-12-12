<?php

namespace App\Http\Controllers;

use App\Models\UserTracking;
use App\Models\User;
use App\Models\Report;
use App\Models\Release;
use App\Models\AnimalFile;
use App\Models\Species;
use App\Models\AnimalStatus;
use App\Services\History\AnimalHistoryTimelineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrazabilidadController extends Controller
{
    /**
     * Obtener todas las acciones realizadas por un voluntario según su CI
     * 
     * @param string $ci CI del voluntario
     * @return JsonResponse
     */
    public function porVoluntario(string $ci): JsonResponse
    {
        // Buscar todos los registros de tracking donde el CI del voluntario que realizó la acción coincida
        // Hacemos join: user_tracking -> users (performed_by) -> people (usuario_id) donde people.ci = {ci}
        // También buscamos si el usuario sobre el que se registra (user_id) tiene ese CI
        $trackings = UserTracking::where(function ($query) use ($ci) {
            // Buscar por performed_by: usuario que realizó la acción
            $query->whereExists(function ($subQuery) use ($ci) {
                $subQuery->select(DB::raw(1))
                         ->from('users')
                         ->join('people', 'users.id', '=', 'people.usuario_id')
                         ->whereColumn('users.id', 'user_tracking.performed_by')
                         ->where('people.ci', $ci);
            })
            // También buscar por user_id: usuario sobre el que se registra la acción
            ->orWhereExists(function ($subQuery) use ($ci) {
                $subQuery->select(DB::raw(1))
                         ->from('users')
                         ->join('people', 'users.id', '=', 'people.usuario_id')
                         ->whereColumn('users.id', 'user_tracking.user_id')
                         ->where('people.ci', $ci);
            });
        })
        ->with(['user.person'])
        ->orderBy('realizado_en', 'desc')
        ->get();
        
        // Obtener todos los IDs de performers únicos para evitar N+1
        $performerIds = $trackings->pluck('performed_by')->filter()->unique();
        $performers = User::whereIn('id', $performerIds)
            ->with('person')
            ->get()
            ->keyBy('id');
        
        $trackings = $trackings->map(function (UserTracking $tracking) use ($performers): array {
            $performer = $tracking->performed_by ? ($performers[$tracking->performed_by] ?? null) : null;
            $performerCi = null;
            $performerName = null;
            
            if ($performer && $performer->person) {
                $performerCi = $performer->person->ci;
                $performerName = $performer->person->nombre;
            }
            
            $user = $tracking->user;
            $userCi = null;
            $userName = null;
            
            if ($user && $user->person) {
                $userCi = $user->person->ci;
                $userName = $user->person->nombre;
            }
            
            $relatedModelInfo = null;
            if ($tracking->related_model_type && $tracking->related_model_id) {
                $relatedModel = $tracking->relatedModel();
                if ($relatedModel) {
                    $relatedModelInfo = [
                        'tipo' => $tracking->related_model_type,
                        'id' => $tracking->related_model_id,
                        'datos' => method_exists($relatedModel, 'toArray') ? $relatedModel->toArray() : null,
                    ];
                }
            }
            
            // Solo devolver valores nuevos relevantes
            $valoresNuevos = $tracking->valores_nuevos ?? [];
            
            // Construir respuesta con solo información relevante
            $respuesta = [
                'id' => $tracking->id,
                'tipo_accion' => $tracking->action_type,
                'descripcion_accion' => $tracking->action_description,
                'realizado_por' => [
                    'id' => $tracking->performed_by,
                    'ci' => $performerCi,
                    'nombre' => $performerName,
                ],
                'fecha' => $tracking->realizado_en ? $tracking->realizado_en->format('d-m-Y H:i:s') : null,
            ];
            
            // Agregar usuario solo si existe
            if ($user) {
                $respuesta['usuario'] = [
                    'id' => $user->id,
                    'ci' => $userCi,
                    'nombre' => $userName,
                ];
            }
            
            // Agregar modelo relacionado solo si existe
            if ($relatedModelInfo) {
                $respuesta['modelo_relacionado'] = $relatedModelInfo;
            }
            
            // Agregar valores nuevos solo si existen y tienen contenido relevante
            if (!empty($valoresNuevos)) {
                $respuesta['valores_nuevos'] = $valoresNuevos;
            }
            
            return $respuesta;
        });

        return response()->json([
            'success' => true,
            'ci_voluntario' => $ci,
            'total_acciones' => $trackings->count(),
            'acciones' => $trackings,
        ]);
    }

    /**
     * Obtener animales hallados y liberados por provincia
     * 
     * @param string $provincia Nombre de la provincia
     * @return JsonResponse
     */
    public function porProvincia(string $provincia): JsonResponse
    {
        // Decodificar la provincia (por si viene URL encoded)
        $provincia = urldecode($provincia);
        
        // Normalizar el nombre de la provincia para búsqueda
        $provinciaNormalizada = $this->normalizeProvinceName($provincia);
        
        // Verificar si buscan "Santa Cruz" (departamento completo)
        $buscarTodasLasProvincias = in_array(mb_strtolower($provinciaNormalizada, 'UTF-8'), [
            'santa cruz',
            'santa cruz de la sierra',
            'cruceña',
            'cruceño'
        ]);
        
        // Lista de todas las provincias del departamento de Santa Cruz
        $provinciasSantaCruz = [
            'Andrés Ibáñez',
            'Ángel Sandoval',
            'Chiquitos',
            'Cordillera',
            'Florida',
            'Germán Busch',
            'Guarayos',
            'Ichilo',
            'Ignacio Warnes',
            'José Miguel de Velasco',
            'Manuel María Caballero',
            'Ñuflo de Chávez',
            'Obispo Santistevan',
            'Sara',
            'Vallegrande',
        ];
        
        // Buscar reportes (hallazgos) en la provincia
        $hallazgos = Report::whereNotNull('direccion')
            ->where('aprobado', true)
            ->with(['animals.animalFiles.species', 'animals.animalFiles.animalStatus', 'condicionInicial', 'incidentType'])
            ->get()
            ->filter(function ($report) use ($provinciaNormalizada, $buscarTodasLasProvincias, $provinciasSantaCruz) {
                $reportProvince = $this->extractProvince($report->direccion);
                
                if (!$reportProvince) {
                    return false;
                }
                
                // Si buscan "Santa Cruz", incluir todas las provincias del departamento
                if ($buscarTodasLasProvincias) {
                    return in_array($reportProvince, $provinciasSantaCruz);
                }
                
                // Comparación sin sensibilidad a mayúsculas, minúsculas y tildes
                return $this->compareStringsIgnoreCaseAndAccents($reportProvince, $provinciaNormalizada);
            })
            ->map(function ($report) {
                $animales = $report->animals->map(function ($animal) use ($report) {
                    $animalFiles = $animal->animalFiles;
                    return [
                        'id' => $animal->id,
                        'nombre' => $animal->nombre,
                        'sexo' => $animal->sexo,
                        'descripcion' => $animal->descripcion,
                        'reporte_id' => $report->id,
                        'fecha_hallazgo' => $report->created_at ? $report->created_at->format('Y-m-d\TH:i:s.000000\Z') : null,
                        'direccion_hallazgo' => $report->direccion,
                        'latitud' => $report->latitud,
                        'longitud' => $report->longitud,
                        'condicion_inicial' => $report->condicionInicial ? $report->condicionInicial->nombre : null,
                        'tipo_incidente' => $report->incidentType ? $report->incidentType->nombre : null,
                        'urgencia' => $report->urgencia,
                        'animal_files' => $animalFiles->map(function ($animalFile) {
                            return [
                                'id' => $animalFile->id,
                                'especie' => $animalFile->species ? $animalFile->species->nombre : null,
                                'estado' => $animalFile->animalStatus ? $animalFile->animalStatus->nombre : null,
                                'imagen_url' => $animalFile->imagen_url,
                            ];
                        }),
                    ];
                });
                
                return [
                    'tipo' => 'hallazgo',
                    'id' => $report->id,
                    'fecha_creacion' => $report->created_at ? $report->created_at->format('Y-m-d\TH:i:s.000000\Z') : null,
                    'direccion' => $report->direccion,
                    'latitud' => $report->latitud,
                    'longitud' => $report->longitud,
                    'observaciones' => $report->observaciones,
                    'animales' => $animales,
                ];
            })
            ->values();

        // Buscar liberaciones en la provincia
        $liberaciones = Release::whereNotNull('direccion')
            ->where('aprobada', true)
            ->with(['animalFile.animal', 'animalFile.species', 'animalFile.animalStatus'])
            ->get()
            ->filter(function ($release) use ($provinciaNormalizada, $buscarTodasLasProvincias, $provinciasSantaCruz) {
                $releaseProvince = $this->extractProvince($release->direccion);
                
                if (!$releaseProvince) {
                    return false;
                }
                
                // Si buscan "Santa Cruz", incluir todas las provincias del departamento
                if ($buscarTodasLasProvincias) {
                    return in_array($releaseProvince, $provinciasSantaCruz);
                }
                
                // Comparación sin sensibilidad a mayúsculas, minúsculas y tildes
                return $this->compareStringsIgnoreCaseAndAccents($releaseProvince, $provinciaNormalizada);
            })
            ->map(function ($release) {
                $animalFile = $release->animalFile;
                $animal = $animalFile ? $animalFile->animal : null;
                
                return [
                    'tipo' => 'liberacion',
                    'id' => $release->id,
                    'fecha_creacion' => $release->created_at ? $release->created_at->format('Y-m-d\TH:i:s.000000\Z') : null,
                    'fecha_liberacion' => $release->created_at ? $release->created_at->format('Y-m-d\TH:i:s.000000\Z') : null,
                    'direccion' => $release->direccion,
                    'latitud' => $release->latitud,
                    'longitud' => $release->longitud,
                    'detalle' => $release->detalle,
                    'imagen_url' => $release->imagen_url,
                    'animal' => $animal ? [
                        'id' => $animal->id,
                        'nombre' => $animal->nombre,
                        'sexo' => $animal->sexo,
                        'descripcion' => $animal->descripcion,
                    ] : null,
                    'animal_file' => $animalFile ? [
                        'id' => $animalFile->id,
                        'especie' => $animalFile->species ? $animalFile->species->nombre : null,
                        'estado' => $animalFile->animalStatus ? $animalFile->animalStatus->nombre : null,
                    ] : null,
                ];
            })
            ->values();

        // Combinar todos los resultados
        $todas = $hallazgos->concat($liberaciones)->sortByDesc('fecha_creacion')->values();

        return response()->json([
            'success' => true,
            'tipo' => 'provincia',
            'query' => $provincia,
            'data' => [
                'hallazgos' => $hallazgos,
                'liberaciones' => $liberaciones,
                'todas' => $todas,
            ],
            'totales' => [
                'hallazgos' => $hallazgos->count(),
                'liberaciones' => $liberaciones->count(),
                'total' => $todas->count(),
            ],
        ]);
    }

    /**
     * Extrae la provincia de una dirección
     * Busca específicamente las provincias del departamento de Santa Cruz
     */
    private function extractProvince(?string $address): ?string
    {
        if (!$address) {
            return null;
        }
        
        // Provincias del departamento de Santa Cruz (en orden de especificidad)
        $provincias = [
            'Andrés Ibáñez',
            'Ángel Sandoval', // También puede aparecer como "Sandóval"
            'Chiquitos',
            'Cordillera',
            'Florida',
            'Germán Busch',
            'Guarayos',
            'Ichilo',
            'Ignacio Warnes', // También puede aparecer como "Warnes"
            'José Miguel de Velasco', // También puede aparecer como "Velasco"
            'Manuel María Caballero',
            'Ñuflo de Chávez',
            'Obispo Santistevan',
            'Sara',
            'Vallegrande',
        ];
        
        // Normalizar la dirección para búsqueda (sin tildes, minúsculas)
        $addressNormalized = $this->normalizeStringForSearch($address);
        
        // Buscar patrón "Provincia X" en la dirección
        if (preg_match('/Provincia\s+([^,]+)/i', $address, $matches)) {
            $provinceFound = trim($matches[1]);
            $provinceFound = preg_replace('/\s+/', ' ', $provinceFound);
            $provinceFoundNormalized = $this->normalizeStringForSearch($provinceFound);
            
            // Verificar si coincide con alguna de nuestras provincias
            foreach ($provincias as $provincia) {
                $provinciaNormalized = $this->normalizeStringForSearch($provincia);
                if (stripos($provinceFoundNormalized, $provinciaNormalized) !== false || 
                    stripos($provinciaNormalized, $provinceFoundNormalized) !== false) {
                    return $provincia;
                }
            }
        }
        
        // Buscar directamente en la dirección (sin el prefijo "Provincia")
        foreach ($provincias as $provincia) {
            $provinciaNormalized = $this->normalizeStringForSearch($provincia);
            
            // Buscar la provincia exacta o variaciones
            $patterns = [
                $provincia,
                // Variaciones comunes
                str_replace('Ignacio Warnes', 'Warnes', $provincia),
                str_replace('José Miguel de Velasco', 'Velasco', $provincia),
                str_replace('Ángel Sandoval', 'Sandóval', $provincia),
            ];
            
            foreach ($patterns as $pattern) {
                $patternNormalized = $this->normalizeStringForSearch($pattern);
                if (stripos($addressNormalized, $patternNormalized) !== false) {
                    return $provincia; // Retornar el nombre canónico
                }
            }
        }
        
        return null;
    }

    /**
     * Normaliza el nombre de la provincia para comparación
     * Convierte variaciones comunes al nombre canónico
     */
    private function normalizeProvinceName(string $provincia): string
    {
        $provincia = trim($provincia);
        
        // Mapeo de variaciones a nombres canónicos
        $normalizaciones = [
            // Santa Cruz (departamento completo)
            'santa cruz' => 'Santa Cruz',
            'santa cruz de la sierra' => 'Santa Cruz',
            'cruceña' => 'Santa Cruz',
            'cruceño' => 'Santa Cruz',
            // Provincias específicas
            'warnes' => 'Ignacio Warnes',
            'ignacio warnes' => 'Ignacio Warnes',
            'velasco' => 'José Miguel de Velasco',
            'jose miguel de velasco' => 'José Miguel de Velasco',
            'josé miguel de velasco' => 'José Miguel de Velasco',
            'sandóval' => 'Ángel Sandoval',
            'angel sandoval' => 'Ángel Sandoval',
            'ángel sandoval' => 'Ángel Sandoval',
            'andres ibañez' => 'Andrés Ibáñez',
            'andrés ibáñez' => 'Andrés Ibáñez',
            'chiquitos' => 'Chiquitos',
            'cordillera' => 'Cordillera',
            'florida' => 'Florida',
            'german busch' => 'Germán Busch',
            'germán busch' => 'Germán Busch',
            'guarayos' => 'Guarayos',
            'ichilo' => 'Ichilo',
            'manuel maria caballero' => 'Manuel María Caballero',
            'manuel maría caballero' => 'Manuel María Caballero',
            'nuflo de chavez' => 'Ñuflo de Chávez',
            'ñuflo de chávez' => 'Ñuflo de Chávez',
            'obispo santistevan' => 'Obispo Santistevan',
            'sara' => 'Sara',
            'vallegrande' => 'Vallegrande',
        ];
        
        $provinciaLower = mb_strtolower($provincia, 'UTF-8');
        
        if (isset($normalizaciones[$provinciaLower])) {
            return $normalizaciones[$provinciaLower];
        }
        
        // Si no está en el mapeo, verificar si coincide exactamente con alguna provincia canónica
        $provinciasCanonicas = [
            'Santa Cruz',
            'Andrés Ibáñez',
            'Ángel Sandoval',
            'Chiquitos',
            'Cordillera',
            'Florida',
            'Germán Busch',
            'Guarayos',
            'Ichilo',
            'Ignacio Warnes',
            'José Miguel de Velasco',
            'Manuel María Caballero',
            'Ñuflo de Chávez',
            'Obispo Santistevan',
            'Sara',
            'Vallegrande',
        ];
        
        foreach ($provinciasCanonicas as $provinciaCanonica) {
            if (mb_strtolower($provinciaCanonica, 'UTF-8') === $provinciaLower) {
                return $provinciaCanonica;
            }
        }
        
        // Si no se encuentra, retornar la original
        return $provincia;
    }

    /**
     * Normaliza un string para búsqueda: convierte a minúsculas y remueve tildes
     */
    private function normalizeStringForSearch(string $str): string
    {
        // Convertir a minúsculas
        $str = mb_strtolower($str, 'UTF-8');
        
        // Remover tildes y caracteres especiales
        $str = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u', 'a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $str
        );
        
        return trim($str);
    }

    /**
     * Compara dos strings ignorando mayúsculas, minúsculas y tildes
     */
    private function compareStringsIgnoreCaseAndAccents(string $str1, string $str2): bool
    {
        return $this->normalizeStringForSearch($str1) === $this->normalizeStringForSearch($str2);
    }

    /**
     * Obtener historial de animales por especie
     * 
     * @param string $especie Nombre de la especie
     * @return JsonResponse
     */
    public function porEspecie(string $especie): JsonResponse
    {
        $especie = urldecode($especie);
        
        // Buscar la especie (insensible a mayúsculas, minúsculas y tildes)
        $species = Species::all()->first(function ($s) use ($especie) {
            return $this->compareStringsIgnoreCaseAndAccents($s->nombre, $especie);
        });
        
        if (!$species) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Especie no encontrada',
                'query' => $especie,
                'data' => [],
                'totales' => ['total' => 0],
            ], 404);
        }
        
        // Buscar todos los animal_files de esta especie
        $animalFiles = AnimalFile::where('especie_id', $species->id)
            ->with([
                'animal.report',
                'species',
                'animalStatus',
                'release',
                'center'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $timelineService = app(AnimalHistoryTimelineService::class);
        
        $animales = $animalFiles->map(function ($animalFile) use ($timelineService) {
            $historial = $timelineService->buildForAnimalFile($animalFile->id);
            $ruta = $timelineService->buildLocationRoute($animalFile->id);
            
            return [
                'id' => $animalFile->id,
                'animal' => $animalFile->animal ? [
                    'id' => $animalFile->animal->id,
                    'nombre' => $animalFile->animal->nombre,
                    'sexo' => $animalFile->animal->sexo,
                    'descripcion' => $animalFile->animal->descripcion,
                ] : null,
                'especie' => $animalFile->species ? [
                    'id' => $animalFile->species->id,
                    'nombre' => $animalFile->species->nombre,
                ] : null,
                'estado' => $animalFile->animalStatus ? [
                    'id' => $animalFile->animalStatus->id,
                    'nombre' => $animalFile->animalStatus->nombre,
                ] : null,
                'centro' => $animalFile->center ? [
                    'id' => $animalFile->center->id,
                    'nombre' => $animalFile->center->nombre,
                ] : null,
                'liberado' => $animalFile->release ? [
                    'id' => $animalFile->release->id,
                    'fecha' => $animalFile->release->created_at ? $animalFile->release->created_at->format('Y-m-d\TH:i:s.000000\Z') : null,
                    'direccion' => $animalFile->release->direccion,
                    'latitud' => $animalFile->release->latitud,
                    'longitud' => $animalFile->release->longitud,
                    'aprobada' => $animalFile->release->aprobada,
                ] : null,
                'reporte' => $animalFile->animal && $animalFile->animal->report ? [
                    'id' => $animalFile->animal->report->id,
                    'fecha' => $animalFile->animal->report->created_at ? $animalFile->animal->report->created_at->format('Y-m-d\TH:i:s.000000\Z') : null,
                    'direccion' => $animalFile->animal->report->direccion,
                ] : null,
                'historial' => $historial,
                'ruta' => $ruta,
                'fecha_creacion' => $animalFile->created_at ? $animalFile->created_at->format('Y-m-d\TH:i:s.000000\Z') : null,
            ];
        });
        
        return response()->json([
            'success' => true,
            'tipo' => 'especie',
            'query' => $especie,
            'especie' => [
                'id' => $species->id,
                'nombre' => $species->nombre,
            ],
            'data' => $animales,
            'totales' => [
                'total' => $animales->count(),
            ],
        ]);
    }

    /**
     * Obtener historial de animales liberados
     * 
     * @return JsonResponse
     */
    public function porLiberados(): JsonResponse
    {
        // Buscar solo animales liberados (con release aprobada)
        $animalFiles = AnimalFile::whereHas('release', function ($q) {
                $q->where('aprobada', true);
            })
            ->with([
                'animal.report',
                'species',
                'animalStatus',
                'release',
                'center'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $timelineService = app(AnimalHistoryTimelineService::class);
        
        $animales = $animalFiles->map(function ($animalFile) use ($timelineService) {
            $historial = $timelineService->buildForAnimalFile($animalFile->id);
            $ruta = $timelineService->buildLocationRoute($animalFile->id);
            
            return [
                'id' => $animalFile->id,
                'animal' => $animalFile->animal ? [
                    'id' => $animalFile->animal->id,
                    'nombre' => $animalFile->animal->nombre,
                    'sexo' => $animalFile->animal->sexo,
                    'descripcion' => $animalFile->animal->descripcion,
                ] : null,
                'especie' => $animalFile->species ? [
                    'id' => $animalFile->species->id,
                    'nombre' => $animalFile->species->nombre,
                ] : null,
                'estado' => $animalFile->animalStatus ? [
                    'id' => $animalFile->animalStatus->id,
                    'nombre' => $animalFile->animalStatus->nombre,
                ] : null,
                'centro' => $animalFile->center ? [
                    'id' => $animalFile->center->id,
                    'nombre' => $animalFile->center->nombre,
                ] : null,
                'liberado' => $animalFile->release ? [
                    'id' => $animalFile->release->id,
                    'fecha' => $animalFile->release->created_at ? $animalFile->release->created_at->format('Y-m-d\TH:i:s.000000\Z') : null,
                    'direccion' => $animalFile->release->direccion,
                    'latitud' => $animalFile->release->latitud,
                    'longitud' => $animalFile->release->longitud,
                    'aprobada' => $animalFile->release->aprobada,
                ] : null,
                'reporte' => $animalFile->animal && $animalFile->animal->report ? [
                    'id' => $animalFile->animal->report->id,
                    'fecha' => $animalFile->animal->report->created_at ? $animalFile->animal->report->created_at->format('Y-m-d\TH:i:s.000000\Z') : null,
                    'direccion' => $animalFile->animal->report->direccion,
                ] : null,
                'historial' => $historial,
                'ruta' => $ruta,
                'fecha_creacion' => $animalFile->created_at ? $animalFile->created_at->format('Y-m-d\TH:i:s.000000\Z') : null,
            ];
        });
        
        return response()->json([
            'success' => true,
            'tipo' => 'liberados',
            'data' => $animales,
            'totales' => [
                'total' => $animales->count(),
            ],
        ]);
    }
}

