<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos base del sistema
        $permissions = [
            // Administración general
            'ver_panel_admin',
            'gestionar_usuarios',

            // Reportes / hallazgos
            'ver_reportes',
            'gestionar_reportes',

            // Animales
            'ver_animales',
            'gestionar_animales',

            // Cuidado y alimentación
            'registrar_cuidados',
            'registrar_alimentacion',

            // Aprobaciones
            'aprobar_rescatistas',
            'aprobar_veterinarios',

            // Veterinaria
            'realizar_evaluaciones_medicas',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Roles
        $adminRole       = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $encargadoRole   = Role::firstOrCreate(['name' => 'encargado', 'guard_name' => 'web']);
        $ciudadanoRole   = Role::firstOrCreate(['name' => 'ciudadano', 'guard_name' => 'web']);
        $cuidadorRole    = Role::firstOrCreate(['name' => 'cuidador', 'guard_name' => 'web']);
        $rescatistaRole  = Role::firstOrCreate(['name' => 'rescatista', 'guard_name' => 'web']);
        $veterinarioRole = Role::firstOrCreate(['name' => 'veterinario', 'guard_name' => 'web']);

        // Admin tiene todos los permisos
        $adminRole->syncPermissions(Permission::all());

        // Encargado: aprueba hallazgos / rescatistas / veterinarios y gestiona reportes y animales
        $encargadoRole->syncPermissions([
            'ver_panel_admin',
            'ver_reportes',
            'gestionar_reportes',
            'ver_animales',
            'gestionar_animales',
            'aprobar_rescatistas',
            'aprobar_veterinarios',
        ]);

        // Ciudadano: acceso básico, por ejemplo ver sus reportes
        $ciudadanoRole->syncPermissions([
            'ver_reportes',
        ]);

        // Cuidador: registrar cuidados / alimentación
        $cuidadorRole->syncPermissions([
            'ver_animales',
            'registrar_cuidados',
            'registrar_alimentacion',
        ]);

        // Rescatista: gestionar reportes y animales en contexto de rescate
        $rescatistaRole->syncPermissions([
            'ver_reportes',
            'gestionar_reportes',
            'ver_animales',
            'gestionar_animales',
        ]);

        // Veterinario: evaluaciones médicas y gestión de animales
        $veterinarioRole->syncPermissions([
            'ver_animales',
            'gestionar_animales',
            'realizar_evaluaciones_medicas',
        ]);
    }
}


