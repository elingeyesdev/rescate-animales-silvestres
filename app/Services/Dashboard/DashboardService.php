<?php

namespace App\Services\Dashboard;

use App\Models\ContactMessage;
use App\Models\Report;
use App\Models\AnimalFile;
use App\Models\Person;
use App\Models\Rescuer;
use App\Models\Veterinarian;
use App\Models\Transfer;
use App\Models\Release;
use App\Models\MedicalEvaluation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    /**
     * Obtiene todos los datos del dashboard según el rol del usuario
     *
     * @return array
     */
    public function getDashboardData(): array
    {
        $user = Auth::user();
        $data = ['user' => $user];

        // Datos comunes para todos los roles
        $data = array_merge($data, $this->getGeneralStatistics());

        // Datos específicos por rol
        if ($user->hasAnyRole(['admin', 'encargado'])) {
            $data = array_merge($data, $this->getAdminDashboardData());
        }

        if ($user->hasRole('veterinario')) {
            $data = array_merge($data, $this->getVeterinarianDashboardData($user));
        }

        if ($user->hasRole('rescatista') && $user->person) {
            $data = array_merge($data, $this->getRescuerDashboardData($user));
        }

        return $data;
    }

    /**
     * Obtiene estadísticas generales visibles para todos los roles
     *
     * @return array
     */
    private function getGeneralStatistics(): array
    {
        return DB::transaction(function () {
            return [
                'totalAnimals' => AnimalFile::count(),
                'releasedAnimals' => Release::count(),
                'totalReports' => Report::count(),
                'approvedReports' => Report::where('aprobado', true)->count(),
                'totalTransfers' => Transfer::count(),
            ];
        });
    }

    /**
     * Obtiene datos del dashboard para administradores y encargados
     *
     * @return array
     */
    private function getAdminDashboardData(): array
    {
        return DB::transaction(function () {
            return [
                // Mensajes de contacto no leídos
                'unreadMessages' => $this->getUnreadMessages(),
                'unreadMessagesCount' => $this->getUnreadMessagesCount(),

                // Reportes pendientes de aprobación
                'pendingReports' => $this->getPendingReports(),
                'pendingReportsCount' => $this->getPendingReportsCount(),

                // Solicitudes pendientes
                'pendingRescuers' => $this->getPendingRescuers(),
                'pendingRescuersCount' => $this->getPendingRescuersCount(),

                'pendingVeterinarians' => $this->getPendingVeterinarians(),
                'pendingVeterinariansCount' => $this->getPendingVeterinariansCount(),

                'pendingCaregivers' => $this->getPendingCaregivers(),
                'pendingCaregiversCount' => $this->getPendingCaregiversCount(),

                // Estadísticas para gráficos
                'reportsByMonth' => $this->getReportsByMonth(),
                'animalsByStatus' => $this->getAnimalsByStatus(),
                'applicationsByType' => $this->getApplicationsByType(),
            ];
        });
    }

    /**
     * Obtiene datos del dashboard para veterinarios
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getVeterinarianDashboardData($user): array
    {
        return DB::transaction(function () use ($user) {
            if (!$user->person) {
                return ['myAnimalFiles' => 0];
            }

            // Buscar el veterinario asociado a la persona del usuario
            $veterinarian = Veterinarian::where('persona_id', $user->person->id)->first();
            
            if (!$veterinarian) {
                return ['myAnimalFiles' => 0];
            }

            // Contar hojas de animales únicas que tienen evaluaciones médicas de este veterinario
            $count = \App\Models\MedicalEvaluation::where('veterinario_id', $veterinarian->id)
                ->whereNotNull('animal_file_id')
                ->distinct('animal_file_id')
                ->count('animal_file_id');

            return ['myAnimalFiles' => $count];
        });
    }

    /**
     * Obtiene datos del dashboard para rescatistas
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getRescuerDashboardData($user): array
    {
        return DB::transaction(function () use ($user) {
            return [
                'myTransfers' => Transfer::where('persona_id', $user->person->id)->count(),
            ];
        });
    }

    /**
     * Obtiene mensajes de contacto no leídos
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUnreadMessages()
    {
        return ContactMessage::where('leido', false)
            ->with('user.person')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene el conteo de mensajes no leídos
     *
     * @return int
     */
    private function getUnreadMessagesCount(): int
    {
        return ContactMessage::where('leido', false)->count();
    }

    /**
     * Obtiene reportes pendientes de aprobación
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingReports()
    {
        return Report::where('aprobado', false)
            ->with('person')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene el conteo de reportes pendientes
     *
     * @return int
     */
    private function getPendingReportsCount(): int
    {
        return Report::where('aprobado', false)->count();
    }

    /**
     * Obtiene rescatistas pendientes de aprobación
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingRescuers()
    {
        return Rescuer::whereNull('aprobado')
            ->with('person')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene el conteo de rescatistas pendientes
     *
     * @return int
     */
    private function getPendingRescuersCount(): int
    {
        return Rescuer::whereNull('aprobado')->count();
    }

    /**
     * Obtiene veterinarios pendientes de aprobación
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingVeterinarians()
    {
        return Veterinarian::whereNull('aprobado')
            ->with('person')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene el conteo de veterinarios pendientes
     *
     * @return int
     */
    private function getPendingVeterinariansCount(): int
    {
        return Veterinarian::whereNull('aprobado')->count();
    }

    /**
     * Obtiene cuidadores pendientes de aprobación
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingCaregivers()
    {
        return Person::where('es_cuidador', true)
            ->whereNull('cuidador_motivo_revision')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtiene el conteo de cuidadores pendientes
     *
     * @return int
     */
    private function getPendingCaregiversCount(): int
    {
        return Person::where('es_cuidador', true)
            ->whereNull('cuidador_motivo_revision')
            ->count();
    }

    /**
     * Obtiene reportes agrupados por mes (últimos 6 meses)
     *
     * @return array
     */
    private function getReportsByMonth(): array
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'pgsql') {
            return Report::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        } else {
            // MySQL/MariaDB
            return Report::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        }
    }

    /**
     * Obtiene animales agrupados por estado
     *
     * @return array
     */
    private function getAnimalsByStatus(): array
    {
        return AnimalFile::join('animal_statuses', 'animal_files.estado_id', '=', 'animal_statuses.id')
            ->select('animal_statuses.nombre', DB::raw('COUNT(*) as count'))
            ->groupBy('animal_statuses.nombre')
            ->get()
            ->pluck('count', 'nombre')
            ->toArray();
    }

    /**
     * Obtiene solicitudes agrupadas por tipo
     *
     * @return array
     */
    private function getApplicationsByType(): array
    {
        return [
            'Rescatistas' => Rescuer::count(),
            'Veterinarios' => Veterinarian::count(),
            'Cuidadores' => Person::where('es_cuidador', true)->count(),
        ];
    }
}

