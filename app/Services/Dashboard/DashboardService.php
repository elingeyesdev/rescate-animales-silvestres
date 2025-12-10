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
use App\Models\Care;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

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
                'transfersByMonth' => $this->getTransfersByMonth(),
                'releasesByMonth' => $this->getReleasesByMonth(),
                'animalFilesByMonth' => $this->getAnimalFilesByMonth(),
                'animalsByStatus' => $this->getAnimalsByStatus(),
                'applicationsByType' => $this->getApplicationsByType(),

                // KPIs de Actividad (presente)
                'animalsBeingRescued' => $this->getAnimalsBeingRescued(),
                'animalsBeingTransferred' => $this->getAnimalsBeingTransferred(),
                'animalsBeingTreated' => $this->getAnimalsBeingTreated(),

                // KPIs de Eficacia
                'efficiencyAttendedRescued' => $this->getEfficiencyAttendedRescued(),
                'efficiencyReadyAttended' => $this->getEfficiencyReadyAttended(),

                // KPI de Efectividad
                'effectivenessReleasedRescued' => $this->getEffectivenessReleasedRescued(),
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
                return [
                    'myAnimalFiles' => 0,
                    'recentEvaluations' => 0,
                    'animalsInTreatment' => 0,
                ];
            }

            // Buscar el veterinario asociado a la persona del usuario
            $veterinarian = Veterinarian::where('persona_id', $user->person->id)->first();
            
            if (!$veterinarian) {
                return [
                    'myAnimalFiles' => 0,
                    'recentEvaluations' => 0,
                    'animalsInTreatment' => 0,
                ];
            }

            // Contar hojas de animales únicas que tienen evaluaciones médicas de este veterinario
            $myAnimalFiles = MedicalEvaluation::where('veterinario_id', $veterinarian->id)
                ->whereNotNull('animal_file_id')
                ->distinct('animal_file_id')
                ->count('animal_file_id');

            // Evaluaciones médicas recientes (últimos 7 días)
            $recentEvaluations = MedicalEvaluation::where('veterinario_id', $veterinarian->id)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count();

            // Animales en tratamiento actualmente (con evaluaciones médicas sin release)
            $animalsInTreatment = MedicalEvaluation::where('veterinario_id', $veterinarian->id)
                ->whereNotNull('animal_file_id')
                ->whereHas('animalFile', function($query) {
                    $query->whereDoesntHave('release');
                })
                ->distinct('animal_file_id')
                ->count('animal_file_id');

            return [
                'myAnimalFiles' => $myAnimalFiles,
                'recentEvaluations' => $recentEvaluations,
                'animalsInTreatment' => $animalsInTreatment,
            ];
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
            if (!$user->person) {
                return [
                    'myTransfers' => 0,
                    'recentTransfers' => 0,
                ];
            }

            // Total de traslados del rescatista
            $myTransfers = Transfer::where('persona_id', $user->person->id)->count();

            // Traslados recientes (últimos 7 días)
            $recentTransfers = Transfer::where('persona_id', $user->person->id)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count();

            return [
                'myTransfers' => $myTransfers,
                'recentTransfers' => $recentTransfers,
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
     * Obtiene traslados agrupados por mes (últimos 6 meses)
     *
     * @return array
     */
    private function getTransfersByMonth(): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            return Transfer::select(
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
            return Transfer::select(
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
     * Obtiene liberaciones agrupadas por mes (últimos 6 meses)
     *
     * @return array
     */
    private function getReleasesByMonth(): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            return Release::select(
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
            return Release::select(
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
     * Obtiene hojas de animales (AnimalFile) agrupadas por mes (últimos 6 meses)
     *
     * @return array
     */
    private function getAnimalFilesByMonth(): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            return AnimalFile::select(
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
            return AnimalFile::select(
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

    /**
     * KPIs DE ACTIVIDAD (Presente)
     */

    /**
     * Cantidad de animales que están siendo rescatados
     * (AnimalFiles creados en los últimos 7 días sin release)
     *
     * @return int
     */
    private function getAnimalsBeingRescued(): int
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        
        return AnimalFile::where('created_at', '>=', $sevenDaysAgo)
            ->whereDoesntHave('release')
            ->count();
    }

    /**
     * Cantidad de animales que están siendo trasladados
     * (Transfers creados en los últimos 7 días donde el animal no tiene release)
     *
     * @return int
     */
    private function getAnimalsBeingTransferred(): int
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        
        // Contar transfers recientes donde el animal no tiene release
        $recentTransfers = Transfer::where('created_at', '>=', $sevenDaysAgo)->get();
        
        $count = 0;
        foreach ($recentTransfers as $transfer) {
            if ($transfer->animal_id) {
                // Traslado interno: verificar que el animal no tenga release
                $animalFileIds = AnimalFile::where('animal_id', $transfer->animal_id)->pluck('id');
                if ($animalFileIds->isNotEmpty() && !Release::whereIn('animal_file_id', $animalFileIds)->exists()) {
                    $count++;
                }
            } elseif ($transfer->reporte_id) {
                // Primer traslado: verificar que el reporte no tenga animal con release
                $report = Report::with('animalFiles.release')->find($transfer->reporte_id);
                if ($report) {
                    // Si no hay animalFiles o ninguno tiene release, contar
                    $hasReleased = $report->animalFiles->contains(function($animalFile) {
                        return $animalFile->release !== null;
                    });
                    if (!$hasReleased) {
                        $count++;
                    }
                }
            }
        }
        
        return $count;
    }

    /**
     * Cantidad de animales que están siendo tratados
     * (AnimalFiles con MedicalEvaluation o Care en los últimos 7 días sin release)
     *
     * @return int
     */
    private function getAnimalsBeingTreated(): int
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        
        // Animales con evaluaciones médicas recientes
        $animalsWithRecentEvaluations = MedicalEvaluation::where('created_at', '>=', $sevenDaysAgo)
            ->whereNotNull('animal_file_id')
            ->pluck('animal_file_id')
            ->unique();
        
        // Animales con cuidados recientes
        $animalsWithRecentCares = Care::where('created_at', '>=', $sevenDaysAgo)
            ->whereNotNull('hoja_animal_id')
            ->pluck('hoja_animal_id')
            ->unique();
        
        // Combinar y contar únicos sin release
        $allAnimalIds = $animalsWithRecentEvaluations->merge($animalsWithRecentCares)->unique();
        
        return AnimalFile::whereIn('id', $allAnimalIds)
            ->whereDoesntHave('release')
            ->count();
    }

    /**
     * KPIs DE EFICACIA
     */

    /**
     * Eficacia: Cantidad de animales atendidos / cantidad de animales rescatados
     * Atendidos = animales que ya tienen hoja de vida, primer traslado o algo más aparte del hallazgo aprobado
     * Rescatados = hallazgos aprobados (reports aprobados)
     *
     * @return array ['attended' => int, 'rescued' => int, 'percentage' => float]
     */
    private function getEfficiencyAttendedRescued(): array
    {
        // Total de hallazgos aprobados (rescatados)
        $totalRescued = Report::where('aprobado', true)->count();
        
        // Animales atendidos = aquellos que tienen:
        // 1. Hoja de vida (AnimalFile) O
        // 2. Primer traslado (Transfer con primer_traslado=true) O
        // 3. Cualquier otra actividad más allá del hallazgo aprobado
        
        // Contar reportes aprobados que tienen al menos una de estas actividades:
        $reportsWithAnimalFiles = Report::where('aprobado', true)
            ->whereHas('animalFiles')
            ->pluck('id');
        
        $reportsWithFirstTransfer = Report::where('aprobado', true)
            ->whereHas('transfers', function($query) {
                $query->where('primer_traslado', true);
            })
            ->pluck('id');
        
        // Combinar reportes que tienen al menos una actividad
        $attendedReportIds = $reportsWithAnimalFiles->merge($reportsWithFirstTransfer)->unique();
        $attended = $attendedReportIds->count();
        
        $percentage = $totalRescued > 0 ? round(($attended / $totalRescued) * 100, 2) : 0;
        
        return [
            'attended' => $attended,
            'rescued' => $totalRescued,
            'percentage' => $percentage,
        ];
    }

    /**
     * Eficacia: Cantidad de animales listos para liberar / cantidad de animales siendo atendidos
     * Listos = animales con estado "Estable"
     * En Atención = cualquier otro animal que ya está en el sistema (ya rescatado y actualmente siendo tratado)
     *
     * @return array ['ready' => int, 'attended' => int, 'percentage' => float]
     */
    private function getEfficiencyReadyAttended(): array
    {
        // Obtener ID del estado "Estable"
        $estableStatusId = \App\Models\AnimalStatus::whereRaw('LOWER(nombre) = ?', ['estable'])->value('id');
        
        // Si no existe "Estable", buscar por LIKE
        if (!$estableStatusId) {
            $estableStatusId = \App\Models\AnimalStatus::whereRaw('LOWER(nombre) LIKE ?', ['%estable%'])->value('id');
        }
        
        // Animales en atención = todos los animales en el sistema (sin release)
        $attended = AnimalFile::whereDoesntHave('release')->count();
        
        // Animales listos (con estado "Estable" y sin release)
        $ready = 0;
        if ($estableStatusId) {
            $ready = AnimalFile::where('estado_id', $estableStatusId)
                ->whereDoesntHave('release')
                ->count();
        }
        
        $percentage = $attended > 0 ? round(($ready / $attended) * 100, 2) : 0;
        
        return [
            'ready' => $ready,
            'attended' => $attended,
            'percentage' => $percentage,
        ];
    }

    /**
     * KPIs DE EFECTIVIDAD
     */

    /**
     * Efectividad: Cantidad de animales liberados / cantidad de animales rescatados
     * (Releases / Total AnimalFiles)
     *
     * @return array ['released' => int, 'rescued' => int, 'percentage' => float]
     */
    private function getEffectivenessReleasedRescued(): array
    {
        $released = Release::count();
        $rescued = AnimalFile::count();
        
        $percentage = $rescued > 0 ? round(($released / $rescued) * 100, 2) : 0;
        
        return [
            'released' => $released,
            'rescued' => $rescued,
            'percentage' => $percentage,
        ];
    }
}

