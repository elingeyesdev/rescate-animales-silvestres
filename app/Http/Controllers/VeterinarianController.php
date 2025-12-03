<?php

namespace App\Http\Controllers;

use App\Models\Veterinarian;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\VeterinarianRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use App\Mail\VeterinarianApplicationResponse;
use App\Services\User\UserTrackingService;

class VeterinarianController extends Controller
{
    public function __construct()
    {
        // Solo administradores o encargados pueden ver veterinarios
        $this->middleware('role:admin|encargado');
        // Administradores y encargados pueden crear veterinarios, solo admin puede eliminar
        $this->middleware('role:admin|encargado')->only(['create','store']);
        $this->middleware('role:admin')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $veterinarians = Veterinarian::with(['person.user'])->paginate();

        return view('veterinarian.index', compact('veterinarians'))
            ->with('i', ($request->input('page', 1) - 1) * $veterinarians->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $veterinarian = new Veterinarian();
        // Preseleccionar persona si viene desde el listado de personas
        $veterinarian->persona_id = $request->query('persona_id');
        // Excluir personas que ya son veterinarios
        $people = Person::whereDoesntHave('veterinarians')
            ->orderBy('nombre')
            ->get(['id','nombre']);
        return view('veterinarian.create', compact('veterinarian','people'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VeterinarianRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('cv')) {
            $data['cv_documentado'] = $request->file('cv')->store('cv', 'public');
        }
        $veterinarian = Veterinarian::create($data);

        // Si ya se crea aprobado, asignar rol al usuario vinculado
        if ($veterinarian->aprobado === true && $veterinarian->person?->user) {
            $veterinarian->person->user->assignRole('veterinario');
        }

        // Registrar tracking de solicitud
        try {
            $user = $veterinarian->person?->user;
            app(UserTrackingService::class)->logApplication(
                'veterinarian',
                $veterinarian,
                $user?->id
            );
        } catch (\Exception $e) {
            \Log::warning('Error registrando tracking de solicitud de veterinario: ' . $e->getMessage());
        }

        return Redirect::route('veterinarians.index')
            ->with('success', 'Veterinario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $veterinarian = Veterinarian::find($id);

        return view('veterinarian.show', compact('veterinarian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $veterinarian = Veterinarian::find($id);
        $people = Person::orderBy('nombre')->get(['id','nombre']);
        return view('veterinarian.edit', compact('veterinarian','people'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VeterinarianRequest $request, Veterinarian $veterinarian): RedirectResponse
    {
        $data = $request->validated();

        // Si es un encargado (y no admin), solo puede cambiar aprobación y motivo de revisión
        $user = Auth::user();
        if ($user && $user->hasRole('encargado') && ! $user->hasRole('admin')) {
            $data = Arr::only($data, ['aprobado', 'motivo_revision']);
        }

        if ($request->hasFile('cv')) {
            $data['cv_documentado'] = $request->file('cv')->store('cv', 'public');
        }
        $oldApproved = $veterinarian->aprobado;
        $veterinarian->update($data);
        $veterinarian->refresh();
        $veterinarian->load('person.user');

        // Enganchar aprobación con roles de Spatie
        $userModel = $veterinarian->person?->user;
        if ($userModel) {
            if ($veterinarian->aprobado === true) {
                $userModel->assignRole('veterinario');
            } elseif ($veterinarian->aprobado === false || $veterinarian->aprobado === null) {
                $userModel->removeRole('veterinario');
            }
        }

        // Registrar tracking si cambió el estado de aprobación
        if ($oldApproved !== $veterinarian->aprobado) {
            try {
                app(UserTrackingService::class)->logVeterinarianApproval(
                    $veterinarian,
                    $veterinarian->aprobado === true,
                    $oldApproved,
                    $veterinarian->motivo_revision
                );
            } catch (\Exception $e) {
                \Log::warning('Error registrando tracking de aprobación de veterinario: ' . $e->getMessage());
            }
        }

        // Enviar correo al ciudadano si cambió el estado de aprobación y hay motivo de revisión
        if ($oldApproved !== $veterinarian->aprobado && !empty($veterinarian->motivo_revision) && $userModel && $userModel->email) {
            try {
                $approved = $veterinarian->aprobado === true;
                Mail::to($userModel->email)->send(new VeterinarianApplicationResponse($veterinarian, $approved));
            } catch (\Exception $e) {
                \Log::error('Error enviando correo de respuesta de solicitud de veterinario: ' . $e->getMessage());
            }
        }

        return Redirect::route('veterinarians.index')
            ->with('success', 'Veterinario actualizado correctamente');
    }

    /**
     * Approve or reject a veterinarian application.
     */
    public function approve(Request $request, Veterinarian $veterinarian): RedirectResponse
    {
        // Solo admin y encargado pueden aprobar/rechazar
        if (!Auth::user()->hasAnyRole(['admin', 'encargado'])) {
            abort(403, 'No tienes permiso para aprobar o rechazar solicitudes de veterinario.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'motivo_revision' => 'required|string|min:3',
        ]);

        $oldApproved = $veterinarian->aprobado;
        $veterinarian->aprobado = $validated['action'] === 'approve' ? true : false;
        $veterinarian->motivo_revision = $validated['motivo_revision'];
        $veterinarian->save();
        $veterinarian->refresh();
        $veterinarian->load('person.user');

        // Enganchar aprobación con roles de Spatie
        $userModel = $veterinarian->person?->user;
        if ($userModel) {
            if ($veterinarian->aprobado === true) {
                $userModel->assignRole('veterinario');
            } elseif ($veterinarian->aprobado === false || $veterinarian->aprobado === null) {
                $userModel->removeRole('veterinario');
            }
        }

        // Registrar tracking de aprobación/rechazo
        if ($oldApproved !== $veterinarian->aprobado) {
            try {
                app(UserTrackingService::class)->logVeterinarianApproval(
                    $veterinarian,
                    $veterinarian->aprobado === true,
                    $oldApproved,
                    $veterinarian->motivo_revision
                );
            } catch (\Exception $e) {
                \Log::warning('Error registrando tracking de aprobación de veterinario: ' . $e->getMessage());
            }
        }

        // Enviar correo al ciudadano si cambió el estado de aprobación
        if ($oldApproved !== $veterinarian->aprobado && $userModel && $userModel->email) {
            try {
                $approved = $veterinarian->aprobado === true;
                Mail::to($userModel->email)->send(new VeterinarianApplicationResponse($veterinarian, $approved));
            } catch (\Exception $e) {
                \Log::error('Error enviando correo de respuesta de solicitud de veterinario: ' . $e->getMessage());
            }
        }

        $message = $validated['action'] === 'approve' 
            ? 'La solicitud de veterinario ha sido aprobada correctamente.' 
            : 'La solicitud de veterinario ha sido rechazada correctamente.';

        return Redirect::route('veterinarians.index')
            ->with('success', $message);
    }

    public function destroy($id): RedirectResponse
    {
        Veterinarian::find($id)->delete();

        return Redirect::route('veterinarians.index')
            ->with('success', 'Veterinario eliminado correctamente');
    }
}
