<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CareFeeding
 *
 * @property $id
 * @property $care_id
 * @property $feeding_type_id
 * @property $feeding_frequency_id
 * @property $feeding_portion_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Care $care
 * @property FeedingFrequency $feedingFrequency
 * @property FeedingPortion $feedingPortion
 * @property FeedingType $feedingType
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CareFeeding extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['care_id', 'feeding_type_id', 'feeding_frequency_id', 'feeding_portion_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function care()
    {
        return $this->belongsTo(\App\Models\Care::class, 'care_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feedingFrequency()
    {
        return $this->belongsTo(\App\Models\FeedingFrequency::class, 'feeding_frequency_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feedingPortion()
    {
        return $this->belongsTo(\App\Models\FeedingPortion::class, 'feeding_portion_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feedingType()
    {
        return $this->belongsTo(\App\Models\FeedingType::class, 'feeding_type_id', 'id');
    }
    
}
