<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistroSimpleController extends Controller
{
    /**
     * GET /api/registro/ci/{ci}
     * Devuelve datos básicos de un usuario por CI para autocompletar registros
     * usados por el API Gateway.
     */
    public function showByCi(Request $request, string $ci)
    {
        $clientSystem = $request->header('X-Client-System', 'unknown');

        Log::info('RegistroSimple lookup recibido', [
            'ci'            => $ci,
            'client_system' => $clientSystem,
            'ip'            => $request->ip(),
        ]);

        $person = Person::where('ci', $ci)->first();

        // NO devolver 404 si no existe
        if (!$person) {
            return response()->json([
                'success' => true,
                'system'  => 'rescate',
                'ci'      => $ci,
                'found'   => false,
                'data'    => null,
            ], 200);
        }

        // Dividir el nombre completo en nombre y apellido
        $nombreCompleto = trim($person->nombre ?? '');
        $nombreParts = preg_split('/\s+/', $nombreCompleto);
        
        // Si hay al menos una palabra, la primera es el nombre
        // El resto son los apellidos
        $nombre = !empty($nombreParts) ? $nombreParts[0] : '';
        $apellido = count($nombreParts) > 1 ? implode(' ', array_slice($nombreParts, 1)) : '';

        return response()->json([
            'success' => true,
            'system'  => 'rescate',
            'ci'      => $ci,
            'found'   => true,
            'data'    => [
                'ci'                 => $person->ci,
                'nombre'             => $nombre,
                'apellido'           => $apellido,
                'telefono'           => $person->telefono,
            ],
        ], 200);
    }
}
