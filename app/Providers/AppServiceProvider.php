<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Programar importación de focos de calor de NASA FIRMS cada 6 horas
        // Esto evita exceder el límite de la API (5000 requests cada 10 minutos)
        Schedule::command('import:nasa-firms 2')
            ->everySixHours()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nasa-firms-import.log'));
    }
}
