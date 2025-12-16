<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Configurar proxies confiables para HTTPS detrás de load balancer/proxy
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO
        );
        // Alias de middlewares de Spatie Permission (roles y permisos)
        // OJO: el namespace correcto es Spatie\Permission\Middleware (sin "s")
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        
        // Habilitar CORS para todas las rutas API
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /**
         * Forzamos respuestas JSON coherentes para las rutas de la API.
         *
         * Esto evita pantallas HTML en blanco o respuestas 200 cuando en realidad
         * hubo un error al consumir los endpoints desde aplicaciones externas
         * (como Postman o apps móviles).
         */
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Solo forzar JSON para rutas de la API (prefijadas con /api)
            if (! $request->is('api/*')) {
                return null; // usar el comportamiento por defecto de Laravel
            }

            // Errores de validación: devolver estructura clara
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'message' => 'Los datos enviados no son válidos.',
                    'errors'  => $e->errors(),
                ], 422);
            }

            // ModelNotFound y 404
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'message' => 'Recurso no encontrado.',
                ], 404);
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return response()->json([
                    'message' => 'Ruta no encontrada.',
                ], 404);
            }

            // Para cualquier otro error, devolver un mensaje genérico
            $status = method_exists($e, 'getStatusCode')
                ? $e->getStatusCode()
                : 500;

            return response()->json([
                'message' => 'Ha ocurrido un error interno en el servidor.',
                'error'   => $e->getMessage(),
            ], $status);
        });
    })->create();
