<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Release
 *
 * @property $id
 * @property $direccion
 * @property $detalle
 * @property $latitud
 * @property $longitud
 * @property $aprobada
 * @property $created_at
 * @property $updated_at
 *
 * @property AnimalFile[] $animalFiles
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Release extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['direccion', 'detalle', 'latitud', 'longitud', 'aprobada'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function animalFiles()
    {
        return $this->hasMany(\App\Models\AnimalFile::class, 'id', 'liberacion_id');
    }
    
}
