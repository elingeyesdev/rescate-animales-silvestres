<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalCondition extends Model
{
    protected $table = 'animal_conditions';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'severidad', // 1..5
        'activo',
    ];
}





