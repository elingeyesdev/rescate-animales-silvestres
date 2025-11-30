<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Person
 *
 * @property $id
 * @property $usuario_id
 * @property $nombre
 * @property $ci
 * @property $telefono
 * @property $foto_path
 * @property $es_cuidador
 * @property $created_at
 * @property $updated_at
 *
 * @property User $user
 * @property Report[] $reports
 * @property Rescuer[] $rescuers
 * @property Veterinarian[] $veterinarians
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Person extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['usuario_id', 'nombre', 'ci', 'telefono', 'foto_path', 'es_cuidador', 'cuidador_center_id', 'cuidador_aprobado', 'cuidador_motivo_revision'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(\App\Models\Report::class, 'persona_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rescuers()
    {
        return $this->hasMany(\App\Models\Rescuer::class, 'persona_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function veterinarians()
    {
        return $this->hasMany(\App\Models\Veterinarian::class, 'persona_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cuidadorCenter()
    {
        return $this->belongsTo(\App\Models\Center::class, 'cuidador_center_id', 'id');
    }

    /**
     * Rol de mayor jerarquía que tiene la persona.
     *
     * Jerarquía de mayor a menor:
     * 1. admin
     * 2. encargado
     * 3. veterinario
     * 4. rescatista
     * 5. cuidador
     * 6. ciudadano
     */
    public function getHighestRoleAttribute(): string
    {
        $user = $this->user;
        if (! $user || ! method_exists($user, 'getRoleNames')) {
            return '-';
        }

        /** @var Collection $roles */
        $roles = $user->getRoleNames();
        if ($roles->isEmpty()) {
            return '-';
        }

        $priority = ['admin', 'encargado', 'veterinario', 'rescatista', 'cuidador', 'ciudadano'];

        foreach ($priority as $role) {
            if ($roles->contains($role)) {
                return ucfirst($role);
            }
        }

        // Si tiene otros roles no previstos en la jerarquía, devolver el primero
        return ucfirst($roles->first());
    }
}
