<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Adoption
 *
 * @property $id
 * @property $direccion
 * @property $latitud
 * @property $longitud
 * @property $detalle
 * @property $aprobada
 * @property $adoptante_id
 * @property $created_at
 * @property $updated_at
 *
 * @property AnimalFile[] $animalFiles
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Adoption extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['direccion', 'latitud', 'longitud', 'detalle', 'aprobada', 'adoptante_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function animalFiles()
    {
        return $this->hasMany(\App\Models\AnimalFile::class, 'id', 'adopcion_id');
    }
    
}
