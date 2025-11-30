<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Report;
use App\Models\AnimalFile;
use App\Models\Person;
use App\Models\Rescuer;
use App\Models\Veterinarian;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $data = [];

        // Datos comunes para todos los roles
        $data['user'] = $user;

        // Datos según el rol
        if ($user->hasRole('admin') || $user->hasRole('encargado')) {
            // Mensajes de contacto no leídos
            $data['unreadMessages'] = ContactMessage::where('leido', false)
                ->with('user.person')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            $data['unreadMessagesCount'] = ContactMessage::where('leido', false)->count();

            // Reportes pendientes de aprobación
            $data['pendingReports'] = Report::where('aprobado', false)
                ->with('person')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            $data['pendingReportsCount'] = Report::where('aprobado', false)->count();

            // Solicitudes pendientes
            $data['pendingRescuers'] = Rescuer::whereNull('aprobado')
                ->with('person')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            $data['pendingRescuersCount'] = Rescuer::whereNull('aprobado')->count();

            $data['pendingVeterinarians'] = Veterinarian::whereNull('aprobado')
                ->with('person')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            $data['pendingVeterinariansCount'] = Veterinarian::whereNull('aprobado')->count();

            // Solicitudes de cuidador pendientes
            $data['pendingCaregivers'] = Person::where('es_cuidador', true)
                ->whereNull('cuidador_motivo_revision')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            $data['pendingCaregiversCount'] = Person::where('es_cuidador', true)
                ->whereNull('cuidador_motivo_revision')
                ->count();

            // Estadísticas para gráficos
            $data['reportsByMonth'] = $this->getReportsByMonth();
            $data['animalsByStatus'] = $this->getAnimalsByStatus();
            $data['applicationsByType'] = $this->getApplicationsByType();
        }

        if ($user->hasRole('veterinario')) {
            $data['myAnimalFiles'] = AnimalFile::whereHas('animal', function($q) use ($user) {
                // Animales que el veterinario ha atendido o creado
            })->count();
        }

        if ($user->hasRole('rescatista') && $user->person) {
            // Buscar traslados donde la persona del usuario es el rescatista
            $data['myTransfers'] = Transfer::where('persona_id', $user->person->id)->count();
        }

        return view('home', $data);
    }

    private function getReportsByMonth()
    {
        // Usar sintaxis compatible con PostgreSQL
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

    private function getAnimalsByStatus()
    {
        return AnimalFile::join('animal_statuses', 'animal_files.estado_id', '=', 'animal_statuses.id')
            ->select('animal_statuses.nombre', DB::raw('COUNT(*) as count'))
            ->groupBy('animal_statuses.nombre')
            ->get()
            ->pluck('count', 'nombre')
            ->toArray();
    }

    private function getApplicationsByType()
    {
        $rescuers = Rescuer::count();
        $veterinarians = Veterinarian::count();
        $caregivers = Person::where('es_cuidador', true)->count();

        return [
            'Rescatistas' => $rescuers,
            'Veterinarios' => $veterinarians,
            'Cuidadores' => $caregivers,
        ];
    }
}
