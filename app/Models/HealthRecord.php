<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class HealthRecord
 *
 * @property $id
 * @property $tipo
 * @property $descripcion
 * @property $tratamiento
 * @property $fecha_revision
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class HealthRecord extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['tipo', 'descripcion', 'tratamiento', 'fecha_revision'];


}
