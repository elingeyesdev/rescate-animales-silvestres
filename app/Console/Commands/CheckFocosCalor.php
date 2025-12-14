<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Fire\FocosCalorService;
use Carbon\Carbon;

class CheckFocosCalor extends Command
{
    protected $signature = 'focos-calor:check';
    protected $description = 'Verificar datos de focos de calor desde APIs externas (NO desde BD)';

    public function handle()
    {
        $this->info('=== VERIFICACIÓN DE FOCOS DE CALOR ===');
        $this->warn('NOTA: Los datos se obtienen de APIs externas, NO de la base de datos.');
        $this->line('');

        $service = app(FocosCalorService::class);
        $hotspots = $service->getRecentHotspotsWithFallback(7);
        
        $total = $hotspots->count();
        $this->info("Total de focos de calor disponibles: {$total}");

        if ($total === 0) {
            $this->warn('⚠️  No se pudieron obtener focos de calor de las APIs externas.');
            $this->line('');
            $this->line('Verifica la configuración:');
            $this->line('  - HOTSPOTS_INTEGRATION_API_URL');
            $this->line('  - NASA_FIRMS_API_KEY');
            return 0;
        }

        $this->line('');
        $this->info('📊 Estadísticas:');
        
        // Por fecha
        $today = $hotspots->filter(function ($h) {
            $acqDate = $h->acq_date instanceof Carbon ? $h->acq_date : Carbon::parse($h->acq_date);
            return $acqDate->isToday();
        })->count();
        
        $yesterday = $hotspots->filter(function ($h) {
            $acqDate = $h->acq_date instanceof Carbon ? $h->acq_date : Carbon::parse($h->acq_date);
            return $acqDate->isYesterday();
        })->count();
        
        $last7Days = $hotspots->count();
        
        $this->table(
            ['Período', 'Cantidad'],
            [
                ['Hoy', $today],
                ['Ayer', $yesterday],
                ['Últimos 7 días', $last7Days],
                ['Total', $total],
            ]
        );

        // Por confianza
        $highConfidence = $hotspots->filter(fn($h) => ($h->confidence ?? 0) >= 70)->count();
        $mediumConfidence = $hotspots->filter(fn($h) => ($h->confidence ?? 0) >= 30 && ($h->confidence ?? 0) < 70)->count();
        $lowConfidence = $hotspots->filter(fn($h) => ($h->confidence ?? 0) < 30 || $h->confidence === null)->count();

        $this->line('');
        $this->info('🎯 Por nivel de confianza:');
        $this->table(
            ['Confianza', 'Cantidad'],
            [
                ['Alta (≥70%)', $highConfidence],
                ['Media (30-69%)', $mediumConfidence],
                ['Baja (<30%)', $lowConfidence],
            ]
        );

        // Últimos 5 focos
        $this->line('');
        $this->info('📍 Últimos 5 focos de calor:');
        $recent = $hotspots->sortByDesc(function ($h) {
            $acqDate = $h->acq_date instanceof Carbon ? $h->acq_date : Carbon::parse($h->acq_date);
            return $acqDate->timestamp;
        })->take(5);

        if ($recent->count() > 0) {
            $this->table(
                ['Fecha', 'Hora', 'Latitud', 'Longitud', 'Confianza', 'FRP'],
                $recent->map(function ($foco) {
                    $acqDate = $foco->acq_date instanceof Carbon ? $foco->acq_date : Carbon::parse($foco->acq_date);
                    return [
                        $acqDate->format('d/m/Y'),
                        $foco->acq_time ?? '00:00:00',
                        number_format($foco->latitude, 4),
                        number_format($foco->longitude, 4),
                        $foco->confidence ? $foco->confidence . '%' : 'N/A',
                        $foco->frp ? number_format($foco->frp, 2) . ' MW' : 'N/A',
                    ];
                })->toArray()
            );
        }

        $this->line('');
        $this->info('✅ Los datos están disponibles desde las APIs externas.');
        $this->line('   Puedes verlos en el mapa de campo: /reports/mapa-campo');

        return 0;
    }
}

