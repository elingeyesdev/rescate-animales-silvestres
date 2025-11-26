<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Transfer
 *
 * @property $id
 * @property $rescatista_id
 * @property $centro_id
 * @property $observaciones
 * @property $created_at
 * @property $updated_at
 *
 * @property Center $center
 * @property Rescuer $rescuer
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Transfer extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['persona_id', 'reporte_id', 'centro_id', 'observaciones', 'primer_traslado', 'animal_id', 'latitud', 'longitud'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function center()
    {
        return $this->belongsTo(\App\Models\Center::class, 'centro_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(\App\Models\Person::class, 'persona_id', 'id');
    }
    
    /**
     * Hallazgo origen (si aplica para primer traslado)
     */
    public function report()
    {
        return $this->belongsTo(\App\Models\Report::class, 'reporte_id', 'id');
    }
    
}
