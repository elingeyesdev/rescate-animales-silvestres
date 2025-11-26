<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalHistory extends Model
{
    protected $table = 'animal_histories';

    public $timestamps = false;

    protected $fillable = [
        'animal_file_id',
        'valores_antiguos',
        'valores_nuevos',
        'observaciones',
    ];

    protected $casts = [
        'valores_antiguos' => 'array',
        'valores_nuevos' => 'array',
        'observaciones' => 'array',
    ];

    /**
     * Relación con la hoja del animal (animal_files).
     */
    public function animalFile()
    {
        return $this->belongsTo(\App\Models\AnimalFile::class, 'animal_file_id', 'id');
    }
}


