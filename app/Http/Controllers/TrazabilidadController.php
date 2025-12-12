<?php

namespace App\Http\Controllers;

use App\Models\UserTracking;
use App\Models\User;
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
            'exito' => true,
            'ci_voluntario' => $ci,
            'total_acciones' => $trackings->count(),
            'acciones' => $trackings,
        ]);
    }
}

