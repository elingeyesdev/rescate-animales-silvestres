<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FocosCalor;
use Carbon\Carbon;

class CheckFocosCalor extends Command
{
    protected $signature = 'focos-calor:check';
    protected $description = 'Verificar datos de focos de calor en la base de datos';

    public function handle()
    {
        $this->info('=== VERIFICACIÓN DE FOCOS DE CALOR ===');
        $this->line('');

        $total = FocosCalor::count();
        $this->info("Total de focos de calor en BD: {$total}");

        if ($total === 0) {
            $this->warn('⚠️  No hay focos de calor en la base de datos.');
            $this->line('');
            $this->line('Para importar datos, ejecuta:');
            $this->line('  php artisan import:nasa-firms 2');
            return 0;
        }

        $this->line('');
        $this->info('📊 Estadísticas:');
        
        // Por fecha
        $today = FocosCalor::whereDate('acq_date', Carbon::today())->count();
        $yesterday = FocosCalor::whereDate('acq_date', Carbon::yesterday())->count();
        $last7Days = FocosCalor::where('acq_date', '>=', Carbon::now()->subDays(7))->count();
        
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
        $highConfidence = FocosCalor::where('confidence', '>=', 70)->count();
        $mediumConfidence = FocosCalor::whereBetween('confidence', [30, 69])->count();
        $lowConfidence = FocosCalor::where('confidence', '<', 30)->orWhereNull('confidence')->count();

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
        $recent = FocosCalor::orderBy('acq_date', 'desc')
            ->orderBy('acq_time', 'desc')
            ->take(5)
            ->get();

        if ($recent->count() > 0) {
            $this->table(
                ['Fecha', 'Hora', 'Latitud', 'Longitud', 'Confianza', 'FRP'],
                $recent->map(function ($foco) {
                    return [
                        $foco->acq_date->format('d/m/Y'),
                        $foco->acq_time,
                        number_format($foco->latitude, 4),
                        number_format($foco->longitude, 4),
                        $foco->confidence ? $foco->confidence . '%' : 'N/A',
                        $foco->frp ? number_format($foco->frp, 2) . ' MW' : 'N/A',
                    ];
                })->toArray()
            );
        }

        $this->line('');
        $this->info('✅ Los datos están disponibles en la base de datos.');
        $this->line('   Puedes verlos en el mapa de campo: /reports/mapa-campo');

        return 0;
    }
}

