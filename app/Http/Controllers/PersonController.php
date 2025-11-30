<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PersonRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class PersonController extends Controller
{
    public function __construct()
    {
        // Debe estar autenticado
        $this->middleware('auth');
        // Solo administradores o encargados pueden ver personas
        $this->middleware('role:admin|encargado');
        // Solo administradores pueden crear, editar, actualizar o eliminar personas
        $this->middleware('role:admin')->only(['create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Person::with('user.roles');

        // Filtro por nombre
        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->input('nombre') . '%');
        }

        // Filtro por email (a través de la relación user)
        if ($request->filled('email')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->input('email') . '%');
            });
        }

        // Filtro por CI
        if ($request->filled('ci')) {
            $query->where('ci', 'like', '%' . $request->input('ci') . '%');
        }

        // Filtro por es_cuidador
        if ($request->filled('es_cuidador')) {
            $esCuidador = $request->input('es_cuidador');
            if ($esCuidador === '1') {
                $query->where('es_cuidador', true);
            } elseif ($esCuidador === '0') {
                $query->where('es_cuidador', false);
            }
        }

        // Filtro por rol (usando roles de Spatie)
        if ($request->filled('rol')) {
            $rol = $request->input('rol');
            $query->whereHas('user.roles', function ($q) use ($rol) {
                $q->where('name', $rol);
            });
        }

        $people = $query->paginate()->withQueryString();
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('person.index', compact('people', 'roles'))
            ->with('i', ($request->input('page', 1) - 1) * $people->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $person = new Person();

        return view('person.create', compact('person'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonRequest $request): RedirectResponse
    {
        Person::create($request->validated());

        return Redirect::route('people.index')
            ->with('success', 'Persona creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $person = Person::with(['rescuers', 'veterinarians', 'user.roles', 'cuidadorCenter'])->findOrFail($id);
        
        // Verificar si la persona es admin
        $personIsAdmin = $person->user && $person->user->hasRole('admin');
        
        // Verificar si ya tiene registros de rescatista o veterinario
        $hasRescuer = $person->rescuers->isNotEmpty();
        $hasVeterinarian = $person->veterinarians->isNotEmpty();
        
        // Verificar si el usuario actual es admin o encargado
        $isAdmin = Auth::check() && Auth::user()->hasRole('admin');
        $isEncargado = Auth::check() && Auth::user()->hasRole('encargado');
        $canApproveCuidador = $isAdmin || $isEncargado;
        
        // Verificar si hay solicitud de cuidador pendiente
        $cuidadorPendiente = (int)$person->es_cuidador === 1 && empty($person->cuidador_motivo_revision);

        return view('person.show', compact('person', 'hasRescuer', 'hasVeterinarian', 'isAdmin', 'isEncargado', 'personIsAdmin', 'canApproveCuidador', 'cuidadorPendiente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $person = Person::with('cuidadorCenter')->findOrFail($id);

        return view('person.edit', compact('person'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonRequest $request, Person $person): RedirectResponse
    {
        $data = $request->validated();
        
        // Si es un encargado (y no admin), solo puede cambiar campos de aprobación de cuidador
        $user = Auth::user();
        if ($user && $user->hasRole('encargado') && !$user->hasRole('admin')) {
            // El encargado solo puede modificar cuidador_aprobado y cuidador_motivo_revision
            $allowedFields = ['cuidador_aprobado', 'cuidador_motivo_revision'];
            $data = array_intersect_key($data, array_flip($allowedFields));
        }
        
        // Detectar si viene la acción de aprobar/rechazar cuidador desde el modal
        if ($request->has('action') && $request->has('cuidador_motivo_revision')) {
            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'cuidador_motivo_revision' => 'required|string|min:3',
            ]);
            
            if ($validated['action'] === 'approve') {
                $data['cuidador_aprobado'] = true;
            } else {
                $data['cuidador_aprobado'] = false;
            }
            
            $data['cuidador_motivo_revision'] = $validated['cuidador_motivo_revision'];
        }
        
        // Actualizar email del usuario si se proporciona
        if ($request->filled('email') && $person->user) {
            $person->user->update(['email' => $request->input('email')]);
        }
        
        // Remover email de $data para que no se intente actualizar en la tabla people
        unset($data['email']);
        
        $person->update($data);

        // Lógica de asignación de rol cuidador
        // Solo se asigna el rol si:
        // 1. es_cuidador = true
        // 2. cuidador_motivo_revision NO es null (fue completado por admin/encargado)
        if ($person->user) {
            if ($person->es_cuidador && !empty($person->cuidador_motivo_revision)) {
                // Asignar rol cuidador
                $role = Role::firstOrCreate(['name' => 'cuidador', 'guard_name' => 'web']);
                $person->user->assignRole($role);
            } else {
                // Remover rol si no cumple las condiciones
                $person->user->removeRole('cuidador');
            }
        }

        // Si fue una acción de aprobación/rechazo, mostrar mensaje específico
        if ($request->has('action')) {
            $message = $request->input('action') === 'approve' 
                ? 'Solicitud de cuidador aprobada correctamente.'
                : 'Solicitud de cuidador rechazada correctamente.';
            return Redirect::route('people.show', $person->id)
                ->with('success', $message);
        }

        return Redirect::route('people.index')
            ->with('success', 'Persona actualizada correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        Person::find($id)->delete();

        return Redirect::route('people.index')
            ->with('success', 'Persona eliminada correctamente');
    }

}
