<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Person
 *
 * @property $id
 * @property $usuario_id
 * @property $nombre
 * @property $ci
 * @property $telefono
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
    protected $fillable = ['usuario_id', 'nombre', 'ci', 'telefono', 'es_cuidador'];


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
        return $this->hasMany(\App\Models\Report::class, 'id', 'persona_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rescuers()
    {
        return $this->hasMany(\App\Models\Rescuer::class, 'id', 'persona_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function veterinarians()
    {
        return $this->hasMany(\App\Models\Veterinarian::class, 'id', 'persona_id');
    }
    
}
