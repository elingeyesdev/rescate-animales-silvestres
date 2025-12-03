<?php

namespace App\Services\User;

use App\Models\UserTracking;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Servicio para registrar actividades y seguimiento de usuarios
 * Similar a AnimalHistory pero para usuarios
 */
class UserTrackingService
{
    /**
     * Registrar una acción de usuario
     *
     * @param string $actionType Tipo de acción (ej: 'registro', 'aprobacion', 'evaluacion_medica')
     * @param string $actionDescription Descripción legible
     * @param int|null $userId Usuario sobre el que se registra (null si es acción del sistema)
     * @param string|null $relatedModelType Tipo de modelo relacionado
     * @param int|null $relatedModelId ID del modelo relacionado
     * @param array|null $oldValues Valores anteriores
     * @param array|null $newValues Valores nuevos
     * @param array|null $metadata Información adicional
     * @param int|null $performedBy Usuario que realizó la acción (null = usuario autenticado)
     * @return UserTracking
     */
    public function log(
        string $actionType,
        string $actionDescription,
        ?int $userId = null,
        ?string $relatedModelType = null,
        ?int $relatedModelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null,
        ?int $performedBy = null
    ): UserTracking {
        // Obtener el ID del usuario que realiza la acción mediante consulta
        // Si no se proporciona, usar el usuario autenticado
        $performerId = $performedBy;
        if ($performerId === null) {
            $performerId = Auth::id();
        }
        
        // Si aún es null, intentar obtenerlo de otra forma (ej: desde el modelo relacionado)
        if ($performerId === null && $relatedModelType && $relatedModelId) {
            $performerId = $this->getPerformerIdFromRelatedModel($relatedModelType, $relatedModelId);
        }

        return UserTracking::create([
            'user_id' => $userId,
            'performed_by' => $performerId, // Se llena mediante consulta, sin foreign key
            'action_type' => $actionType,
            'action_description' => $actionDescription,
            'related_model_type' => $relatedModelType,
            'related_model_id' => $relatedModelId,
            'valores_antiguos' => $oldValues,
            'valores_nuevos' => $newValues,
            'metadata' => $metadata,
            'realizado_en' => Carbon::now(),
        ]);
    }

    /**
     * Obtener el ID del usuario que realizó la acción desde el modelo relacionado
     * Útil cuando la acción se realiza sobre un modelo que tiene relación con usuario
     */
    private function getPerformerIdFromRelatedModel(string $modelType, int $modelId): ?int
    {
        try {
            $modelClass = 'App\\Models\\' . $modelType;
            if (!class_exists($modelClass)) {
                return null;
            }

            $model = $modelClass::find($modelId);
            if (!$model) {
                return null;
            }

            // Intentar obtener el usuario desde diferentes relaciones comunes
            if (method_exists($model, 'person') && $model->person) {
                return $model->person->usuario_id ?? null;
            }

            if (method_exists($model, 'user')) {
                return $model->user?->id;
            }

            if (isset($model->usuario_id)) {
                return $model->usuario_id;
            }

            if (isset($model->user_id)) {
                return $model->user_id;
            }

            return null;
        } catch (\Exception $e) {
            \Log::warning("Error obteniendo performed_by desde modelo relacionado: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Registrar registro de nuevo usuario
     */
    public function logUserRegistration(User $user, ?array $metadata = null): UserTracking
    {
        return $this->log(
            actionType: 'registro',
            actionDescription: "Usuario registrado: {$user->email}",
            userId: $user->id,
            newValues: [
                'user_id' => $user->id,
                'email' => $user->email,
                'person' => $user->person ? [
                    'id' => $user->person->id,
                    'nombre' => $user->person->nombre,
                    'ci' => $user->person->ci,
                ] : null,
            ],
            metadata: $metadata
        );
    }

    /**
     * Registrar aprobación/rechazo de veterinario
     * Registra en el seguimiento del usuario sobre el que se actúa Y del que realiza la acción
     */
    public function logVeterinarianApproval(
        Model $veterinarian,
        bool $approved,
        ?bool $oldApproved = null,
        ?string $motivo = null
    ): array {
        $veterinarian->load('person.user');
        $user = $veterinarian->person?->user;
        $personName = $veterinarian->person?->nombre ?? 'N/A';
        $performerId = Auth::id();
        
        $action = $approved ? 'aprobacion' : 'rechazo';
        $description = $approved 
            ? "Veterinario aprobado: {$personName}"
            : "Veterinario rechazado: {$personName}";
        $performerDescription = $approved
            ? "Aprobaste a veterinario: {$personName}"
            : "Rechazaste a veterinario: {$personName}";

        // Registro en el seguimiento del usuario sobre el que se actúa
        $tracking1 = $this->log(
            actionType: $action,
            actionDescription: $description,
            userId: $user?->id,
            relatedModelType: 'Veterinarian',
            relatedModelId: $veterinarian->id,
            oldValues: $oldApproved !== null ? ['aprobado' => $oldApproved] : null,
            newValues: [
                'aprobado' => $approved,
                'veterinario_id' => $veterinarian->id,
                'persona_id' => $veterinarian->persona_id,
                'motivo_revision' => $motivo,
            ],
            metadata: [
                'veterinarian' => [
                    'id' => $veterinarian->id,
                    'especialidad' => $veterinarian->especialidad,
                ],
                'person' => $veterinarian->person ? [
                    'id' => $veterinarian->person->id,
                    'nombre' => $veterinarian->person->nombre,
                ] : null,
            ],
            performedBy: $performerId
        );

        // Registro en el seguimiento del usuario que realiza la acción
        $tracking2 = $this->log(
            actionType: $action,
            actionDescription: $performerDescription,
            userId: $performerId, // El que realiza la acción también aparece en su propio seguimiento
            relatedModelType: 'Veterinarian',
            relatedModelId: $veterinarian->id,
            oldValues: $oldApproved !== null ? ['aprobado' => $oldApproved] : null,
            newValues: [
                'aprobado' => $approved,
                'veterinario_id' => $veterinarian->id,
                'persona_id' => $veterinarian->persona_id,
                'motivo_revision' => $motivo,
            ],
            metadata: [
                'veterinarian' => [
                    'id' => $veterinarian->id,
                    'especialidad' => $veterinarian->especialidad,
                ],
                'person' => $veterinarian->person ? [
                    'id' => $veterinarian->person->id,
                    'nombre' => $veterinarian->person->nombre,
                ] : null,
                'target_user_id' => $user?->id, // ID del usuario sobre el que se actuó
            ],
            performedBy: $performerId
        );

        return [$tracking1, $tracking2];
    }

    /**
     * Registrar aprobación/rechazo de rescatista
     * Registra en el seguimiento del usuario sobre el que se actúa Y del que realiza la acción
     */
    public function logRescuerApproval(
        Model $rescuer,
        bool $approved,
        ?bool $oldApproved = null,
        ?string $motivo = null
    ): array {
        $rescuer->load('person.user');
        $user = $rescuer->person?->user;
        $personName = $rescuer->person?->nombre ?? 'N/A';
        $performerId = Auth::id();
        
        $action = $approved ? 'aprobacion' : 'rechazo';
        $description = $approved 
            ? "Rescatista aprobado: {$personName}"
            : "Rescatista rechazado: {$personName}";
        $performerDescription = $approved
            ? "Aprobaste a rescatista: {$personName}"
            : "Rechazaste a rescatista: {$personName}";

        // Registro en el seguimiento del usuario sobre el que se actúa
        $tracking1 = $this->log(
            actionType: $action,
            actionDescription: $description,
            userId: $user?->id,
            relatedModelType: 'Rescuer',
            relatedModelId: $rescuer->id,
            oldValues: $oldApproved !== null ? ['aprobado' => $oldApproved] : null,
            newValues: [
                'aprobado' => $approved,
                'rescuer_id' => $rescuer->id,
                'persona_id' => $rescuer->persona_id,
                'motivo_revision' => $motivo,
            ],
            metadata: [
                'rescuer' => [
                    'id' => $rescuer->id,
                ],
                'person' => $rescuer->person ? [
                    'id' => $rescuer->person->id,
                    'nombre' => $rescuer->person->nombre,
                ] : null,
            ],
            performedBy: $performerId
        );

        // Registro en el seguimiento del usuario que realiza la acción
        $tracking2 = $this->log(
            actionType: $action,
            actionDescription: $performerDescription,
            userId: $performerId,
            relatedModelType: 'Rescuer',
            relatedModelId: $rescuer->id,
            oldValues: $oldApproved !== null ? ['aprobado' => $oldApproved] : null,
            newValues: [
                'aprobado' => $approved,
                'rescuer_id' => $rescuer->id,
                'persona_id' => $rescuer->persona_id,
                'motivo_revision' => $motivo,
            ],
            metadata: [
                'rescuer' => [
                    'id' => $rescuer->id,
                ],
                'person' => $rescuer->person ? [
                    'id' => $rescuer->person->id,
                    'nombre' => $rescuer->person->nombre,
                ] : null,
                'target_user_id' => $user?->id,
            ],
            performedBy: $performerId
        );

        return [$tracking1, $tracking2];
    }

    /**
     * Registrar aprobación/rechazo de cuidador
     * Registra en el seguimiento del usuario sobre el que se actúa Y del que realiza la acción
     */
    public function logCaregiverApproval(
        Model $person,
        bool $approved,
        ?bool $oldApproved = null,
        ?string $motivo = null
    ): array {
        $person->load('user');
        $user = $person->user;
        $personName = $person->nombre ?? 'N/A';
        $performerId = Auth::id();
        
        $action = $approved ? 'aprobacion' : 'rechazo';
        $description = $approved 
            ? "Cuidador aprobado: {$personName}"
            : "Cuidador rechazado: {$personName}";
        $performerDescription = $approved
            ? "Aprobaste a cuidador: {$personName}"
            : "Rechazaste a cuidador: {$personName}";

        // Registro en el seguimiento del usuario sobre el que se actúa
        $tracking1 = $this->log(
            actionType: $action,
            actionDescription: $description,
            userId: $user?->id,
            relatedModelType: 'Person',
            relatedModelId: $person->id,
            oldValues: $oldApproved !== null ? ['cuidador_aprobado' => $oldApproved] : null,
            newValues: [
                'cuidador_aprobado' => $approved,
                'persona_id' => $person->id,
                'cuidador_motivo_revision' => $motivo,
            ],
            metadata: [
                'person' => [
                    'id' => $person->id,
                    'nombre' => $person->nombre,
                ],
            ],
            performedBy: $performerId
        );

        // Registro en el seguimiento del usuario que realiza la acción
        $tracking2 = $this->log(
            actionType: $action,
            actionDescription: $performerDescription,
            userId: $performerId,
            relatedModelType: 'Person',
            relatedModelId: $person->id,
            oldValues: $oldApproved !== null ? ['cuidador_aprobado' => $oldApproved] : null,
            newValues: [
                'cuidador_aprobado' => $approved,
                'persona_id' => $person->id,
                'cuidador_motivo_revision' => $motivo,
            ],
            metadata: [
                'person' => [
                    'id' => $person->id,
                    'nombre' => $person->nombre,
                ],
                'target_user_id' => $user?->id,
            ],
            performedBy: $performerId
        );

        return [$tracking1, $tracking2];
    }

    /**
     * Registrar evaluación médica realizada por veterinario
     */
    public function logMedicalEvaluation(
        Model $medicalEvaluation,
        ?Model $animalFile = null
    ): UserTracking {
        $medicalEvaluation->load('veterinarian.person.user', 'animalFile.animal');
        $veterinarian = $medicalEvaluation->veterinarian;
        $user = $veterinarian?->person?->user;
        $animal = $animalFile?->animal ?? $medicalEvaluation->animalFile?->animal;

        $animalName = $animal?->nombre ?? 'Animal sin nombre';
        $description = "Evaluación médica realizada a: {$animalName}";

        return $this->log(
            actionType: 'evaluacion_medica',
            actionDescription: $description,
            userId: $user?->id,
            relatedModelType: 'MedicalEvaluation',
            relatedModelId: $medicalEvaluation->id,
            newValues: [
                'medical_evaluation_id' => $medicalEvaluation->id,
                'veterinarian_id' => $medicalEvaluation->veterinario_id,
                'animal_file_id' => $medicalEvaluation->animal_file_id,
                'diagnostico' => $medicalEvaluation->diagnostico,
                'fecha' => $medicalEvaluation->fecha?->format('d/m/Y'),
            ],
            metadata: [
                'veterinarian' => $veterinarian ? [
                    'id' => $veterinarian->id,
                    'persona_id' => $veterinarian->persona_id,
                    'nombre' => $veterinarian->person?->nombre,
                ] : null,
                'animal' => $animal ? [
                    'id' => $animal->id,
                    'nombre' => $animal->nombre,
                ] : null,
                'animal_file' => $animalFile ? [
                    'id' => $animalFile->id,
                ] : null,
            ]
        );
    }

    /**
     * Registrar aprobación de reporte
     */
    public function logReportApproval(
        Model $report,
        bool $approved,
        ?bool $oldApproved = null
    ): UserTracking {
        $report->load('person.user');
        $user = $report->person?->user;

        $action = $approved ? 'aprobacion' : 'rechazo';
        $description = $approved 
            ? "Reporte aprobado: N°{$report->id}"
            : "Reporte rechazado: N°{$report->id}";

        return $this->log(
            actionType: $action,
            actionDescription: $description,
            userId: $user?->id,
            relatedModelType: 'Report',
            relatedModelId: $report->id,
            oldValues: $oldApproved !== null ? ['aprobado' => $oldApproved] : null,
            newValues: [
                'aprobado' => $approved,
                'report_id' => $report->id,
            ],
            metadata: [
                'report' => [
                    'id' => $report->id,
                    'direccion' => $report->direccion,
                    'urgencia' => $report->urgencia,
                ],
            ]
        );
    }

    /**
     * Registrar creación de solicitud (veterinario, rescatista, cuidador, etc.)
     */
    public function logApplication(
        string $applicationType,
        Model $application,
        ?int $userId = null
    ): UserTracking {
        $description = match($applicationType) {
            'veterinarian' => "Solicitud de veterinario creada",
            'rescuer' => "Solicitud de rescatista creada",
            'caregiver' => "Solicitud de cuidador creada",
            default => "Solicitud creada",
        };

        return $this->log(
            actionType: 'solicitud',
            actionDescription: $description,
            userId: $userId ?? Auth::id(),
            relatedModelType: class_basename($application),
            relatedModelId: $application->id,
            newValues: [
                'application_type' => $applicationType,
                'application_id' => $application->id,
            ],
            metadata: [
                'application_type' => $applicationType,
            ],
            performedBy: Auth::id()
        );
    }

    /**
     * Registrar creación de reporte/hallazgo
     */
    public function logReportCreation(
        Model $report,
        ?int $userId = null
    ): UserTracking {
        $report->load('person.user', 'condicionInicial', 'incidentType');
        $user = $report->person?->user ?? ($userId ? User::find($userId) : null);

        $description = "Registro de hallazgo creado: N°{$report->id}";
        if ($report->direccion) {
            $description .= " - {$report->direccion}";
        }

        return $this->log(
            actionType: 'reporte_creado',
            actionDescription: $description,
            userId: $user?->id,
            relatedModelType: 'Report',
            relatedModelId: $report->id,
            newValues: [
                'reporte_id' => $report->id,
                'direccion' => $report->direccion,
                'urgencia' => $report->urgencia,
                'tipo_incidente_id' => $report->tipo_incidente_id,
                'condicion_inicial_id' => $report->condicion_inicial_id,
            ],
            metadata: [
                'reporte' => [
                    'id' => $report->id,
                    'direccion' => $report->direccion,
                    'urgencia' => $report->urgencia,
                    'tipo_incidente' => $report->incidentType?->nombre,
                    'condicion_inicial' => $report->condicionInicial?->nombre,
                ],
            ]
        );
    }

    /**
     * Registrar traslado (primer traslado o traslado interno)
     */
    public function logTransfer(
        Model $transfer,
        bool $isFirstTransfer = false
    ): UserTracking {
        $transfer->load('person.user', 'center', 'report');
        $user = $transfer->person?->user;

        $description = $isFirstTransfer
            ? "Primer traslado registrado desde reporte N°{$transfer->reporte_id}"
            : "Traslado interno registrado";

        if ($transfer->center) {
            $description .= " hacia: {$transfer->center->nombre}";
        }

        return $this->log(
            actionType: 'traslado',
            actionDescription: $description,
            userId: $user?->id,
            relatedModelType: 'Transfer',
            relatedModelId: $transfer->id,
            newValues: [
                'transfer_id' => $transfer->id,
                'centro_id' => $transfer->centro_id,
                'reporte_id' => $transfer->reporte_id,
                'animal_id' => $transfer->animal_id,
                'primer_traslado' => $isFirstTransfer,
            ],
            metadata: [
                'transfer' => [
                    'id' => $transfer->id,
                    'centro' => $transfer->center?->nombre,
                    'reporte_id' => $transfer->reporte_id,
                    'animal_id' => $transfer->animal_id,
                    'primer_traslado' => $isFirstTransfer,
                ],
            ]
        );
    }

    /**
     * Registrar cuidado realizado
     */
    public function logCare(
        Model $care,
        ?Model $animalFile = null
    ): UserTracking {
        $care->load('animalFile.animal', 'careType');
        $animalFile = $animalFile ?? $care->animalFile;
        $animal = $animalFile?->animal;

        $animalName = $animal?->nombre ?? 'Animal sin nombre';
        $careTypeName = $care->careType?->nombre ?? 'Cuidado';
        $description = "Cuidado '{$careTypeName}' registrado para: {$animalName}";

        return $this->log(
            actionType: 'cuidado',
            actionDescription: $description,
            userId: Auth::id(), // El usuario autenticado que registra el cuidado
            relatedModelType: 'Care',
            relatedModelId: $care->id,
            newValues: [
                'care_id' => $care->id,
                'tipo_cuidado_id' => $care->tipo_cuidado_id,
                'hoja_animal_id' => $care->hoja_animal_id,
                'fecha' => $care->fecha?->format('d/m/Y'),
            ],
            metadata: [
                'care' => [
                    'id' => $care->id,
                    'tipo_cuidado' => $careTypeName,
                    'descripcion' => $care->descripcion,
                ],
                'animal' => $animal ? [
                    'id' => $animal->id,
                    'nombre' => $animal->nombre,
                ] : null,
            ]
        );
    }

    /**
     * Registrar alimentación
     */
    public function logFeeding(
        Model $careFeeding,
        ?Model $care = null
    ): UserTracking {
        $care = $care ?? $careFeeding->care;
        $care->load('animalFile.animal', 'careType');
        $animal = $care->animalFile?->animal;

        $animalName = $animal?->nombre ?? 'Animal sin nombre';
        $description = "Alimentación registrada para: {$animalName}";

        return $this->log(
            actionType: 'alimentacion',
            actionDescription: $description,
            userId: Auth::id(),
            relatedModelType: 'CareFeeding',
            relatedModelId: $careFeeding->id,
            newValues: [
                'care_feeding_id' => $careFeeding->id,
                'care_id' => $careFeeding->care_id,
                'tipo_alimentacion_id' => $careFeeding->feeding_type_id,
                'frecuencia_id' => $careFeeding->feeding_frequency_id,
                'porcion_id' => $careFeeding->feeding_portion_id,
            ],
            metadata: [
                'care_feeding' => [
                    'id' => $careFeeding->id,
                ],
                'animal' => $animal ? [
                    'id' => $animal->id,
                    'nombre' => $animal->nombre,
                ] : null,
            ]
        );
    }

    /**
     * Registrar liberación de animal
     */
    public function logRelease(
        Model $release,
        ?Model $animalFile = null
    ): UserTracking {
        $release->load('animalFile.animal');
        $animalFile = $animalFile ?? $release->animalFile;
        $animal = $animalFile?->animal;

        $animalName = $animal?->nombre ?? 'Animal sin nombre';
        $description = "Liberación registrada para: {$animalName}";
        if ($release->direccion) {
            $description .= " en {$release->direccion}";
        }

        return $this->log(
            actionType: 'liberacion',
            actionDescription: $description,
            userId: Auth::id(),
            relatedModelType: 'Release',
            relatedModelId: $release->id,
            newValues: [
                'release_id' => $release->id,
                'animal_file_id' => $release->animal_file_id,
                'direccion' => $release->direccion,
                'latitud' => $release->latitud,
                'longitud' => $release->longitud,
                'aprobada' => $release->aprobada,
            ],
            metadata: [
                'release' => [
                    'id' => $release->id,
                    'direccion' => $release->direccion,
                ],
                'animal' => $animal ? [
                    'id' => $animal->id,
                    'nombre' => $animal->nombre,
                ] : null,
            ]
        );
    }

    /**
     * Registrar actualización de perfil
     */
    public function logProfileUpdate(
        Model $person,
        ?array $oldValues = null,
        ?array $newValues = null
    ): UserTracking {
        $person->load('user');
        $user = $person->user;

        $description = "Perfil actualizado: {$person->nombre}";

        return $this->log(
            actionType: 'actualizacion_perfil',
            actionDescription: $description,
            userId: $user?->id ?? Auth::id(),
            relatedModelType: 'Person',
            relatedModelId: $person->id,
            oldValues: $oldValues,
            newValues: $newValues ?? [
                'persona_id' => $person->id,
                'nombre' => $person->nombre,
                'ci' => $person->ci,
                'telefono' => $person->telefono,
            ],
            metadata: [
                'person' => [
                    'id' => $person->id,
                    'nombre' => $person->nombre,
                ],
            ],
            performedBy: Auth::id()
        );
    }

    /**
     * Obtener historial de un usuario
     */
    public function getUserHistory(int $userId, ?int $limit = null)
    {
        $query = UserTracking::where('user_id', $userId)
            ->orderBy('realizado_en', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Obtener todas las actividades realizadas por un usuario
     */
    public function getActivitiesByPerformer(int $performerId, ?int $limit = null)
    {
        $query = UserTracking::where('performed_by', $performerId)
            ->orderBy('realizado_en', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Obtener actividades por tipo de acción
     */
    public function getActivitiesByType(string $actionType, ?int $limit = null)
    {
        $query = UserTracking::where('action_type', $actionType)
            ->orderBy('realizado_en', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}

