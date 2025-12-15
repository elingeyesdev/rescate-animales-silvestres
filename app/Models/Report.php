<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transfer;

/**
 * Class Report
 *
 * @property $id
 * @property $persona_id
 * @property $aprobado
 * @property $imagen_url
 * @property $observaciones
 * @property $created_at
 * @property $updated_at
 *
 * @property Person $person
 * @property Animal[] $animals
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Report extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'persona_id',
        'aprobado',
        'imagen_url',
        'observaciones',
        'latitud',
        'longitud',
        'direccion',
        // nuevos campos parametrizables
        'condicion_inicial_id',
        'tipo_incidente_id',
        'tamano',
        'puede_moverse',
        'urgencia',
        'incendio_id',
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
    public function animalFiles()
    {
        // Compatibilidad: obtener animal_files a través de animals (report -> animals -> animal_files)
        return $this->hasManyThrough(
            \App\Models\AnimalFile::class,
            \App\Models\Animal::class,
            'reporte_id',   // Foreign key on animals referencing reports.id
            'animal_id',    // Foreign key on animal_files referencing animals.id
            'id',           // Local key on reports
            'id'            // Local key on animals
        );
    }
    
    /**
     * Animales relacionados (reporte tiene muchos animales)
     */
    public function animals()
    {
        return $this->hasMany(\App\Models\Animal::class, 'reporte_id', 'id');
    }

    /**
     * Condición observada (catálogo)
     */
    public function condicionInicial()
    {
        return $this->belongsTo(\App\Models\AnimalCondition::class, 'condicion_inicial_id', 'id');
    }

    /**
     * Tipo de incidente (catálogo)
     */
    public function incidentType()
    {
        return $this->belongsTo(\App\Models\IncidentType::class, 'tipo_incidente_id', 'id');
    }

    /**
     * Traslados asociados a este hallazgo (vía campo reporte_id en transfers).
     */
    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'reporte_id', 'id');
    }

    /**
     * Primer traslado registrado para este hallazgo (si existe).
     */
    public function firstTransfer()
    {
        return $this->hasOne(Transfer::class, 'reporte_id', 'id')
            ->where('primer_traslado', true);
    }

    /**
     * Obtener focos de calor cercanos a este reporte (por proximidad geográfica)
     * 
     * NOTA: No hay relación directa por ID porque la API de NASA FIRMS
     * no proporciona IDs de incendios. La relación se hace por coordenadas.
     * 
     * @param float $radiusKm Radio en kilómetros (default: 20 km)
     * @param int|null $days Días hacia atrás (default: 2)
     * @return \Illuminate\Support\Collection
     */
    public function getNearbyFocosCalor(float $radiusKm = 20, ?int $days = 2): \Illuminate\Support\Collection
    {
        if (!$this->latitud || !$this->longitud) {
            return collect();
        }

        $service = app(\App\Services\Fire\FocosCalorService::class);
        return $service->getNearbyHotspots(
            (float) $this->latitud,
            (float) $this->longitud,
            $radiusKm,
            $days
        );
    }

    /**
     * Determina el estado actual del hallazgo basado en su progreso
     * 
     * @return string Estado: 'En Peligro', 'En Traslado', 'Tratado', o 'Liberado'
     */
    public function getEstado(): string
    {
        // Cargar relaciones necesarias si no están cargadas
        if (!$this->relationLoaded('animals')) {
            $this->load('animals.animalFiles.release');
        }
        if (!$this->relationLoaded('firstTransfer')) {
            $this->load('firstTransfer');
        }

        $animals = $this->animals;
        $animalFiles = $animals->flatMap->animalFiles;
        $hasAnimalFile = $animalFiles->isNotEmpty();
        
        // Buscar si hay release
        $hasRelease = $animalFiles->contains(function($animalFile) {
            return $animalFile->release !== null;
        });
        
        // Verificar si hay primer traslado
        $hasFirstTransfer = $this->firstTransfer !== null;

        // Clasificar según estado (orden de prioridad: Liberado > Tratado > En Traslado > En Peligro)
        if ($hasRelease) {
            return 'Liberado';
        } elseif ($hasAnimalFile) {
            return 'Tratado';
        } elseif ($hasFirstTransfer) {
            return 'En Traslado';
        } else {
            return 'En Peligro';
        }
    }

    /**
     * Obtiene la clase CSS del badge según el estado
     * 
     * @return string Clase CSS del badge
     */
    public function getEstadoBadgeClass(): string
    {
        $estado = $this->getEstado();
        switch ($estado) {
            case 'Liberado':
                return 'badge-info';
            case 'Tratado':
                return 'badge-success';
            case 'En Traslado':
                return 'badge-warning';
            case 'En Peligro':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }
}
