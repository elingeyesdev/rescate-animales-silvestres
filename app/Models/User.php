<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Person;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación uno a uno con Person.
     */
    public function person()
    {
        return $this->hasOne(Person::class, 'usuario_id', 'id');
    }

    /**
     * Nombre "lógico" del usuario para mostrar en el header.
     * Como la tabla users no tiene columna name, usamos:
     * - El nombre de la persona asociada, o
     * - El email como fallback.
     */
    public function getNameAttribute(): string
    {
        return $this->person->nombre ?? $this->attributes['email'] ?? '';
    }

    /**
     * URL del perfil de usuario para el menú de AdminLTE.
     */
    public function adminlte_profile_url()
    {
        // Se interpretará como url('profile') porque use_route_url = false
        return 'profile';
    }

    /**
     * Descripción que muestra AdminLTE para el usuario.
     * Se usa en el header del menú de usuario (dropdown), no en el toggler.
     */
    public function adminlte_desc(): ?string
    {
        if (! method_exists($this, 'getRoleNames')) {
            return null;
        }

        $roles = $this->getRoleNames();
        if ($roles->isEmpty()) {
            return null;
        }

        return $roles->map(fn ($r) => ucfirst($r))->implode(', ');
    }
}
