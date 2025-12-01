<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AnimalProfile
 *
 * @property $id
 * @property $estado_salud
 * @property $sexo
 * @property $especie
 * @property $alimentacion
 * @property $frecuencia
 * @property $cantidad
 * @property $color
 * @property $imagen
 * @property $reporte_id
 * @property $detalle
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class AnimalProfile extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['estado_salud', 'sexo', 'especie', 'alimentacion', 'frecuencia', 'cantidad', 'color', 'imagen', 'reporte_id', 'detalle'];


}
