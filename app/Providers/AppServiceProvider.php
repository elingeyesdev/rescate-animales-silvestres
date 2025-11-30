<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        /**
         * Permitir usar el nombre de un rol como "ability" en Gate/@can().
         *
         * Esto hace que:
         *   @can('admin')  ->  $user->hasRole('admin')
         * y también que las opciones 'can' del menú de AdminLTE funcionen con nombres de rol.
         */
        Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'hasRole') && $user->hasRole($ability)) {
                return true;
            }

            return null; // continuar con las comprobaciones normales de permisos
        });
    }
}
