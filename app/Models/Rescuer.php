<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Rescuer
 *
 * @property $id
 * @property $persona_id
 * @property $cv_documentado
 * @property $created_at
 * @property $updated_at
 *
 * @property Person $person
 * @property Transfer[] $transfers
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Rescuer extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['persona_id', 'cv_documentado', 'aprobado', 'motivo_revision'];

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
    public function transfers()
    {
        return $this->hasMany(\App\Models\Transfer::class, 'id', 'rescatista_id');
    }
    
}
