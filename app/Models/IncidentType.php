<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentType extends Model
{
    protected $table = 'incident_types';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'riesgo', // 0 bajo, 1 medio, 2 alto
        'activo',
    ];
}





