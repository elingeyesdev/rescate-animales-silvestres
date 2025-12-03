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

    protected $description = 'Import NASA FIRMS fire hotspot data into the database';

    // NASA FIRMS API configuration
    private const NASA_API_KEY = '1ae0346a287432156ada4abb791d57cd';
    private const NASA_API_BASE = 'https://firms.modaps.eosdis.nasa.gov/api/area/csv';
    
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
        
        $this->info("Fetching NASA FIRMS data for the last {$days} days...");
        
        // Try multiple satellites
        $satellites = ['VIIRS_NOAA21_NRT', 'VIIRS_SNPP_NRT', 'MODIS_NRT'];
        $allData = [];
        
        foreach ($satellites as $satellite) {
            $this->info("Trying satellite: {$satellite}...");
            
            // Use area endpoint with Bolivia bounds
            $url = sprintf(
                '%s/%s/%s/%s,%s,%s,%s/%s',
                self::NASA_API_BASE,
                self::NASA_API_KEY,
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

