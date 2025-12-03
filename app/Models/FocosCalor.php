<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para almacenar focos de calor (hotspots) de la API de NASA FIRMS
 * 
 * @property int $id
 * @property float $latitude
 * @property float $longitude
 * @property int|null $confidence
 * @property \Carbon\Carbon $acq_date
 * @property string $acq_time
 * @property float|null $bright_ti4
 * @property float|null $bright_ti5
 * @property float|null $frp
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class FocosCalor extends Model
{
    protected $table = 'focos_calor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'latitude',
        'longitude',
        'confidence',
        'acq_date',
        'acq_time',
        'bright_ti4',
        'bright_ti5',
        'frp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'confidence' => 'integer',
        'acq_date' => 'date',
        'bright_ti4' => 'float',
        'bright_ti5' => 'float',
        'frp' => 'float',
    ];

    /**
     * Obtener reportes cercanos a este foco de calor (por proximidad geográfica)
     * 
     * NOTA: No hay relación directa por ID. La relación se hace por coordenadas.
     * 
     * @param float $radiusKm Radio en kilómetros (default: 10 km)
     * @return \Illuminate\Support\Collection
     */
    public function getNearbyReports(float $radiusKm = 10): \Illuminate\Support\Collection
    {
        return Report::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get()
            ->filter(function ($report) use ($radiusKm) {
                $distance = $this->distanceTo(
                    (float) $report->latitud,
                    (float) $report->longitud
                );
                return $distance <= $radiusKm;
            });
    }

    /**
     * Calcular distancia en kilómetros desde este foco a unas coordenadas
     *
     * @param float $lat
     * @param float $lng
     * @return float Distancia en kilómetros
     */
    public function distanceTo(float $lat, float $lng): float
    {
        $earthRadius = 6371; // Radio de la Tierra en kilómetros

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

