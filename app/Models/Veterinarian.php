<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Veterinarian
 *
 * @property $id
 * @property $especialidad
 * @property $cv_documentado
 * @property $persona_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Person $person
 * @property MedicalEvaluation[] $medicalEvaluations
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Veterinarian extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['especialidad', 'cv_documentado', 'persona_id', 'aprobado', 'motivo_revision'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'aprobado' => 'boolean',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(\App\Models\Person::class, 'persona_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function medicalEvaluations()
    {
        return $this->hasMany(\App\Models\MedicalEvaluation::class, 'id', 'veterinario_id');
    }
    
}
