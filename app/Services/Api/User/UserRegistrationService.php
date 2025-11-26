<?php

namespace App\Services\Api\User;

use App\Models\User;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

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

            return [
                'user'   => $user,
                'person' => $person,
            ];
        });
    }
}
