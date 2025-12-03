<?php

namespace App\Services\Api\User;

use App\Models\User;
use App\Models\Person;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Services\User\UserTrackingService;

class UserRegistrationService
{
    /**
     * Registra un usuario y su persona asociada en una transacción.
     *
     * @param  array  $data
     * @return array{user: User, person: Person}
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // El modelo User tiene cast \"password\" => \"hashed\",
            // por lo que al asignar el valor plano se encripta automáticamente.
            $user = User::create([
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);

            $person = Person::create([
                'usuario_id' => $user->id,
                'nombre'     => $data['nombre'],
                'ci'         => $data['ci'],
                'telefono'   => $data['telefono'],
                'es_cuidador'=> $data['es_cuidador'] ?? false,
            ]);

            // Rol por defecto: ciudadano (se asegura que exista aunque el seeder no se haya ejecutado)
            if (method_exists($user, 'assignRole')) {
                $role = Role::firstOrCreate(['name' => 'ciudadano', 'guard_name' => 'web']);
                $user->assignRole($role);
            }

            // Registrar tracking de registro
            try {
                app(UserTrackingService::class)->logUserRegistration($user, [
                    'person' => [
                        'id' => $person->id,
                        'nombre' => $person->nombre,
                        'ci' => $person->ci,
                    ],
                ]);
            } catch (\Exception $e) {
                // No fallar el registro si el tracking falla
                \Log::warning('Error registrando tracking de usuario (API): ' . $e->getMessage());
            }

            return [
                'user'   => $user,
                'person' => $person,
            ];
        });
    }
}
