<?php

namespace App\Menu\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Auth;

class SpatieRoleFilter implements FilterInterface
{
    /**
     * Transforma el menú para verificar roles de Spatie.
     *
     * @param array $item
     * @return array|null
     */
    public function transform($item)
    {
        // Si el item no tiene 'can', mostrarlo siempre
        if (!isset($item['can'])) {
            return $item;
        }

        // Si el usuario no está autenticado, ocultar el item
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();

        // Lista de roles conocidos del sistema
        $knownRoles = ['admin', 'encargado', 'ciudadano', 'cuidador', 'rescatista', 'veterinario'];

        // Si el campo 'can' contiene '|', significa que es una lista de roles separados por pipe
        $roles = explode('|', $item['can']);
        $roles = array_map('trim', $roles);
        $roles = array_map('strtolower', $roles);

        // Verificar si alguno de los roles en 'can' es un rol conocido de Spatie
        $isSpatieRole = false;
        foreach ($roles as $role) {
            if (in_array(strtolower($role), $knownRoles)) {
                $isSpatieRole = true;
                break;
            }
        }

        // Si es un rol de Spatie, procesarlo
        if ($isSpatieRole) {
            // Verificar si el usuario tiene alguno de los roles especificados
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    // Remover 'can' para evitar que GateFilter lo procese después
                    unset($item['can']);
                    return $item;
                }
            }
            // Si el usuario no tiene ninguno de los roles, ocultar el item
            return null;
        }

        // Si no es un rol de Spatie, dejar que otros filtros (como GateFilter) lo procesen
        return $item;
    }
}

