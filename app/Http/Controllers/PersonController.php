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
use App\Services\User\UserTrackingService;

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

        $people = $query->with(['rescuers', 'veterinarians'])->paginate()->withQueryString();
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

        // Obtener el tracking del usuario si tiene usuario asociado
        $userTracking = [];
        if ($person->user) {
            try {
                $trackingService = app(UserTrackingService::class);
                $userTracking = $trackingService->getUserHistory($person->user->id);
            } catch (\Exception $e) {
                \Log::warning('Error obteniendo tracking del usuario: ' . $e->getMessage());
            }
        }

        return view('person.show', compact('person', 'hasRescuer', 'hasVeterinarian', 'isAdmin', 'isEncargado', 'personIsAdmin', 'canApproveCuidador', 'cuidadorPendiente', 'userTracking'));
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
        // Detectar si viene la acción de aprobar/rechazar cuidador desde el modal
        // Verificar ANTES de la validación de PersonRequest
        $hasAction = $request->has('action') && $request->filled('action');
        $hasMotivo = $request->filled('cuidador_motivo_revision');
        $isCuidadorAction = $hasAction && $hasMotivo;
        
        if ($isCuidadorAction) {
            // Validar específicamente para la acción de cuidador (bypass PersonRequest)
            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'cuidador_motivo_revision' => 'required|string|min:3',
            ]);
            
            // Preparar datos para actualización
            $data = [
                'cuidador_aprobado' => $validated['action'] === 'approve' ? true : false,
                'cuidador_motivo_revision' => trim($validated['cuidador_motivo_revision']),
            ];
            
            // Asegurarse de que es_cuidador esté en true (es necesario para la aprobación)
            // Si se aprueba, es_cuidador debe ser true
            if ($validated['action'] === 'approve') {
                $data['es_cuidador'] = true;
            } else {
                // Si se rechaza, mantener es_cuidador como está pero limpiar aprobación
                $data['es_cuidador'] = $person->es_cuidador ?? false;
            }
        } else {
            // Validación normal usando PersonRequest
            $data = $request->validated();
            
            // Si es un encargado (y no admin), solo puede cambiar campos de aprobación de cuidador
            $user = Auth::user();
            if ($user && $user->hasRole('encargado') && !$user->hasRole('admin')) {
                // El encargado solo puede modificar cuidador_aprobado y cuidador_motivo_revision
                $allowedFields = ['cuidador_aprobado', 'cuidador_motivo_revision'];
                $data = array_intersect_key($data, array_flip($allowedFields));
            }
        }
        
        // Actualizar email del usuario si se proporciona
        if ($request->filled('email') && $person->user) {
            $person->user->update(['email' => $request->input('email')]);
        }
        
        // Remover email de $data para que no se intente actualizar en la tabla people
        unset($data['email']);
        
        // Actualizar la persona - usar fill y save para asegurar que se guarden todos los campos
        $person->fill($data);
        $saved = $person->save();
        
        // Si no se guardó correctamente, lanzar error
        if (!$saved) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Error al actualizar la información del cuidador.');
        }
        
        // Refrescar el modelo para obtener los valores actualizados
        $person->refresh();

        // Lógica de asignación de rol cuidador
        // Solo se asigna el rol si:
        // 1. es_cuidador = true
        // 2. cuidador_motivo_revision NO es null (fue completado por admin/encargado)
        // 3. cuidador_aprobado = true (si fue aprobado)
        if ($person->user) {
            // Usar comparación estricta con los valores cast del modelo
            $esCuidador = (bool) $person->es_cuidador;
            $aprobado = (bool) $person->cuidador_aprobado;
            $tieneMotivo = !empty(trim($person->cuidador_motivo_revision ?? ''));
            
            $shouldHaveRole = $esCuidador && $tieneMotivo && $aprobado;
            
            if ($shouldHaveRole) {
                // Asignar rol cuidador
                $role = Role::firstOrCreate(['name' => 'cuidador', 'guard_name' => 'web']);
                if (!$person->user->hasRole('cuidador')) {
                    $person->user->assignRole($role);
                }
            } else {
                // Remover rol si no cumple las condiciones
                if ($person->user->hasRole('cuidador')) {
                    $person->user->removeRole('cuidador');
                }
            }
        }

        // Si fue una acción de aprobación/rechazo, mostrar mensaje específico
        if ($isCuidadorAction) {
            $action = $request->input('action');
            $oldApproved = $person->getOriginal('cuidador_aprobado');
            
            // Registrar tracking de aprobación/rechazo de cuidador
            try {
                app(UserTrackingService::class)->logCaregiverApproval(
                    $person,
                    $action === 'approve',
                    $oldApproved,
                    $person->cuidador_motivo_revision
                );
            } catch (\Exception $e) {
                //
            }
            
            if ($action === 'approve') {
                $message = 'Solicitud de cuidador aprobada correctamente.';
                if ($person->user && $person->user->hasRole('cuidador')) {
                    $message .= ' El rol de cuidador ha sido asignado.';
                }
            } else {
                $message = 'Solicitud de cuidador rechazada correctamente.';
            }
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

    /**
     * Convert a person to encargado role
     */
    public function convertToEncargado($id): RedirectResponse
    {
        // Solo administradores pueden convertir en encargado
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'No tiene permisos para realizar esta acción.');
        }

        $person = Person::with('user')->findOrFail($id);

        // Verificar que la persona tenga un usuario asociado
        if (!$person->user) {
            return Redirect::back()
                ->with('error', 'La persona no tiene un usuario asociado.');
        }

        // Verificar que no sea admin
        if ($person->user->hasRole('admin')) {
            return Redirect::back()
                ->with('error', 'No se puede convertir un administrador en encargado.');
        }

        // Verificar que no tenga ya el rol de encargado
        if ($person->user->hasRole('encargado')) {
            return Redirect::back()
                ->with('info', 'La persona ya tiene el rol de encargado.');
        }

        // Asignar rol de encargado
        $role = Role::firstOrCreate(['name' => 'encargado', 'guard_name' => 'web']);
        $person->user->assignRole($role);

        return Redirect::route('people.show', $person->id)
            ->with('success', 'La persona ha sido convertida en encargado correctamente.');
    }

}
