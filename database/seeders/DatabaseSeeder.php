<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles y permisos base
        $this->call(RolesAndPermissionsSeeder::class);

        // Usuario administrador por defecto (idempotente)
        $admin = User::firstOrCreate(
            ['email' => 'crs6000919@est.univalle.edu'],
            ['password' => 'crs6000919'] // se encripta automáticamente por el cast "hashed"
        );

        // Asignar rol admin
        $admin->assignRole('admin');
    }
}
