<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\FocosCalor;
use App\Services\Fire\FocosCalorService;
use Carbon\Carbon;

class ImportNasaFirms extends Command
{
    protected $signature = 'import:nasa-firms {days=2 : Number of days to import}';

    protected $description = 'Import fire hotspot data (from integration API or NASA FIRMS) into the database';

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
        $days = $this->argument('days');
        
        // Intentar primero obtener datos del endpoint de integración
        $integrationData = $this->fetchFromIntegrationApi();
        
        if ($integrationData !== null) {
            $this->info("✓ Successfully fetched data from integration API");
            return $this->processIntegrationData($integrationData);
        }
        
        // Si falla, usar NASA FIRMS como fallback
        $this->info("Integration API unavailable, falling back to NASA FIRMS...");
        return $this->fetchFromNasaFirms($days);
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

    /**
     * Procesar datos del endpoint de integración
     */
    private function processIntegrationData(array $hotspots): int
    {
        if (empty($hotspots)) {
            $this->warn('No hotspot data to import');
            return 0;
        }

        $this->info('Processing ' . count($hotspots) . ' hotspots from integration API...');
        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $outsideBolivia = 0;

        $progressBar = $this->output->createProgressBar(count($hotspots));

        foreach ($hotspots as $hotspot) {
            try {
                // Validar campos requeridos
                if (!isset($hotspot['latitude']) || !isset($hotspot['longitude'])) {
                    $errors++;
                    $progressBar->advance();
                    continue;
                }

                $lat = (float) $hotspot['latitude'];
                $lng = (float) $hotspot['longitude'];
                
                // Filter for Bolivia
                if ($lat < self::BOLIVIA_BOUNDS['min_lat'] || 
                    $lat > self::BOLIVIA_BOUNDS['max_lat'] ||
                    $lng < self::BOLIVIA_BOUNDS['min_lng'] || 
                    $lng > self::BOLIVIA_BOUNDS['max_lng']) {
                    $outsideBolivia++;
                    $progressBar->advance();
                    continue;
                }

                // Convertir fecha de DD/MM/YYYY a formato Carbon
                $dateStr = $hotspot['date'] ?? null;
                if ($dateStr) {
                    // Parsear formato DD/MM/YYYY
                    $dateParts = explode('/', $dateStr);
                    if (count($dateParts) === 3) {
                        $acqDate = Carbon::createFromDate($dateParts[2], $dateParts[1], $dateParts[0]);
                    } else {
                        $acqDate = Carbon::parse($dateStr);
                    }
                } else {
                    $acqDate = Carbon::today();
                }

                // Convertir tiempo de "412" a "04:12:00"
                $timeStr = $hotspot['time'] ?? '0000';
                $timeStr = str_pad($timeStr, 4, '0', STR_PAD_LEFT);
                $formattedTime = substr($timeStr, 0, 2) . ':' . substr($timeStr, 2, 2) . ':00';

                // Check if already exists
                $exists = FocosCalor::where('latitude', $lat)
                    ->where('longitude', $lng)
                    ->where('acq_date', $acqDate->format('Y-m-d'))
                    ->where('acq_time', $formattedTime)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Handle confidence (convertir "n", "l", "h" a números)
                $confidence = $hotspot['confidence'] ?? null;
                if (!is_numeric($confidence)) {
                    $confidenceMap = ['l' => 0, 'n' => 50, 'h' => 100];
                    $confidence = $confidenceMap[strtolower($confidence)] ?? 50;
                } else {
                    $confidence = (int) $confidence;
                }

                // Mapear brightness a bright_ti4 o bright_ti5 según el satélite
                $brightness = isset($hotspot['brightness']) ? (float) $hotspot['brightness'] : null;
                $satellite = $hotspot['satellite'] ?? '';
                
                // Determinar si es VIIRS (bright_ti4) o MODIS (bright_ti5)
                $brightTi4 = null;
                $brightTi5 = null;
                if (stripos($satellite, 'VIIRS') !== false || stripos($satellite, 'N21') !== false || stripos($satellite, 'SNPP') !== false) {
                    $brightTi4 = $brightness;
                } else {
                    $brightTi5 = $brightness;
                }

                // Create new hotspot record
                FocosCalor::create([
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'confidence' => $confidence,
                    'acq_date' => $acqDate,
                    'acq_time' => $formattedTime,
                    'bright_ti4' => $brightTi4,
                    'bright_ti5' => $brightTi5,
                    'frp' => isset($hotspot['frp']) ? (float) $hotspot['frp'] : null,
                ]);

                $imported++;
            } catch (\Exception $e) {
                if ($errors == 0) {
                    $this->error("First error: " . $e->getMessage());
                }
                $errors++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info("Import completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Imported', $imported],
                ['Skipped (duplicates)', $skipped],
                ['Outside Bolivia', $outsideBolivia],
                ['Errors', $errors],
                ['Total processed', count($hotspots)],
            ]
        );

        if ($imported > 0) {
            $this->info("✓ Successfully imported {$imported} new hotspots");
            
            // Limpiar caché para que se muestren los nuevos datos
            $service = app(FocosCalorService::class);
            $service->clearCache();
            $this->info("✓ Cache cleared - new data will be visible immediately");
        }

        return 0;
    }

    /**
     * Obtener datos de NASA FIRMS (método original)
     */
    private function fetchFromNasaFirms(int $days): int
    {
        $this->info("Fetching NASA FIRMS data for the last {$days} days...");
        
        // Try multiple satellites
        $satellites = ['VIIRS_NOAA21_NRT', 'VIIRS_SNPP_NRT', 'MODIS_NRT'];
        $allData = [];
        
        foreach ($satellites as $satellite) {
            $this->info("Trying satellite: {$satellite}...");
            
            // Use area endpoint with Bolivia bounds
            $url = sprintf(
                '%s/%s/%s/%s,%s,%s,%s/%s',
                $this->getNasaApiBase(),
                $this->getNasaApiKey(),
                $satellite,
                self::BOLIVIA_BOUNDS['min_lng'],
                self::BOLIVIA_BOUNDS['min_lat'],
                self::BOLIVIA_BOUNDS['max_lng'],
                self::BOLIVIA_BOUNDS['max_lat'],
                $days
            );

            try {
                $response = Http::timeout(60)->get($url);
                
                if ($response->successful()) {
                    $csvData = $response->body();
                    $lines = explode("\n", trim($csvData));
                    
                    if (count($lines) > 1) {
                        $this->info("  ✓ Got " . (count($lines) - 1) . " records from {$satellite}");
                        $allData = array_merge($allData, $lines);
                    } else {
                        $this->warn("  - No data from {$satellite}");
                    }
                }
            } catch (\Exception $e) {
                $this->warn("  - Failed to fetch from {$satellite}: " . $e->getMessage());
            }
        }
        
        if (empty($allData) || count($allData) < 2) {
            $this->warn('No hotspot data available from any NASA FIRMS satellite');
            return 0;
        }

        // Remove duplicate headers
        $headers = null;
        $dataLines = [];
        foreach ($allData as $line) {
            if (strpos($line, 'latitude') !== false) {
                if (!$headers) {
                    $headers = str_getcsv($line);
                }
            } else if (!empty(trim($line))) {
                $dataLines[] = $line;
            }
        }
        
        if (!$headers || empty($dataLines)) {
            $this->warn('No valid data to import');
            return 0;
        }

        $this->info('Processing ' . count($dataLines) . ' hotspots...');
        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $outsideBolivia = 0;

        $progressBar = $this->output->createProgressBar(count($dataLines));

        foreach ($dataLines as $line) {
            $values = str_getcsv($line);
            
            if (count($values) < count($headers)) {
                $errors++;
                $progressBar->advance();
                continue;
            }

            $fire = array_combine($headers, $values);
            
            // Filter for Bolivia
            $lat = (float) $fire['latitude'];
            $lng = (float) $fire['longitude'];
            
            if ($lat < self::BOLIVIA_BOUNDS['min_lat'] || 
                $lat > self::BOLIVIA_BOUNDS['max_lat'] ||
                $lng < self::BOLIVIA_BOUNDS['min_lng'] || 
                $lng > self::BOLIVIA_BOUNDS['max_lng']) {
                $outsideBolivia++;
                $progressBar->advance();
                continue;
            }
            
            try {
                // Format time (HHMM -> HH:MM:00)
                $time = str_pad($fire['acq_time'], 4, '0', STR_PAD_LEFT);
                $formattedTime = substr($time, 0, 2) . ':' . substr($time, 2, 2) . ':00';

                // Check if already exists
                $exists = FocosCalor::where('latitude', $lat)
                    ->where('longitude', $lng)
                    ->where('acq_date', $fire['acq_date'])
                    ->where('acq_time', $formattedTime)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Handle confidence
                $confidence = $fire['confidence'] ?? null;
                if (!is_numeric($confidence)) {
                    $confidenceMap = ['l' => 0, 'n' => 50, 'h' => 100];
                    $confidence = $confidenceMap[strtolower($confidence)] ?? 0;
                }

                // Create new hotspot record
                FocosCalor::create([
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'confidence' => $confidence,
                    'acq_date' => Carbon::parse($fire['acq_date']),
                    'acq_time' => $formattedTime,
                    'bright_ti4' => isset($fire['bright_ti4']) ? (float) $fire['bright_ti4'] : null,
                    'bright_ti5' => isset($fire['bright_ti5']) ? (float) $fire['bright_ti5'] : null,
                    'frp' => isset($fire['frp']) ? (float) $fire['frp'] : null,
                ]);

                $imported++;
            } catch (\Exception $e) {
                if ($errors == 0) {
                    $this->error("First error: " . $e->getMessage());
                }
                $errors++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info("Import completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Imported', $imported],
                ['Skipped (duplicates)', $skipped],
                ['Outside Bolivia', $outsideBolivia],
                ['Errors', $errors],
                ['Total processed', count($dataLines)],
            ]
        );

        if ($imported > 0) {
            $this->info("✓ Successfully imported {$imported} new hotspots");
            
            // Limpiar caché para que se muestren los nuevos datos
            $service = app(FocosCalorService::class);
            $service->clearCache();
            $this->info("✓ Cache cleared - new data will be visible immediately");
        }

        return 0;
    }
}

