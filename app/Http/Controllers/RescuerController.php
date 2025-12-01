<?php

namespace App\Http\Controllers;

use App\Models\Rescuer;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RescuerRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use App\Mail\RescuerApplicationResponse;

class RescuerController extends Controller
{
    public function __construct()
    {
        // Solo administradores o encargados pueden ver rescatistas
        $this->middleware('role:admin|encargado');
        // Administradores y encargados pueden crear rescatistas, solo admin puede eliminar
        $this->middleware('role:admin|encargado')->only(['create','store']);
        $this->middleware('role:admin')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $rescuers = Rescuer::with(['person.user'])->paginate();

        return view('rescuer.index', compact('rescuers'))
            ->with('i', ($request->input('page', 1) - 1) * $rescuers->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $rescuer = new Rescuer();
        $rescuer->persona_id = $request->query('persona_id');
        // Excluir personas que ya son rescatistas
        $people = Person::whereDoesntHave('rescuers')
            ->orderBy('nombre')
            ->get(['id','nombre']);
        return view('rescuer.create', compact('rescuer','people'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RescuerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('cv')) {
            $data['cv_documentado'] = $request->file('cv')->store('cv', 'public');
        }
        $rescuer = Rescuer::create($data);

        // Si ya se crea aprobado, asignar rol al usuario vinculado
        if ($rescuer->aprobado === true && $rescuer->person?->user) {
            $rescuer->person->user->assignRole('rescatista');
        }

        return Redirect::route('rescuers.index')
            ->with('success', 'Rescatista creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $rescuer = Rescuer::find($id);

        return view('rescuer.show', compact('rescuer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $rescuer = Rescuer::find($id);
        $people = Person::orderBy('nombre')->get(['id','nombre']);
        return view('rescuer.edit', compact('rescuer','people'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RescuerRequest $request, Rescuer $rescuer): RedirectResponse
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
        $oldApproved = $rescuer->aprobado;
        $rescuer->update($data);
        $rescuer->refresh();
        $rescuer->load('person.user');

        // Enganchar aprobación con roles de Spatie
        $userModel = $rescuer->person?->user;
        if ($userModel) {
            if ($rescuer->aprobado === true) {
                $userModel->assignRole('rescatista');
            } elseif ($rescuer->aprobado === false || $rescuer->aprobado === null) {
                $userModel->removeRole('rescatista');
            }
        }

        // Enviar correo al ciudadano si cambió el estado de aprobación y hay motivo de revisión
        if ($oldApproved !== $rescuer->aprobado && !empty($rescuer->motivo_revision) && $userModel && $userModel->email) {
            try {
                $approved = $rescuer->aprobado === true;
                Mail::to($userModel->email)->send(new RescuerApplicationResponse($rescuer, $approved));
            } catch (\Exception $e) {
                \Log::error('Error enviando correo de respuesta de solicitud de rescatista: ' . $e->getMessage());
            }
        }

        return Redirect::route('rescuers.index')
            ->with('success', 'Rescatista actualizado correctamente');
    }

    /**
     * Approve or reject a rescuer application.
     */
    public function approve(Request $request, Rescuer $rescuer): RedirectResponse
    {
        // Solo admin y encargado pueden aprobar/rechazar
        if (!Auth::user()->hasAnyRole(['admin', 'encargado'])) {
            abort(403, 'No tienes permiso para aprobar o rechazar solicitudes de rescatista.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'motivo_revision' => 'required|string|min:3',
        ]);

        $oldApproved = $rescuer->aprobado;
        $rescuer->aprobado = $validated['action'] === 'approve' ? true : false;
        $rescuer->motivo_revision = $validated['motivo_revision'];
        $rescuer->save();
        $rescuer->refresh();
        $rescuer->load('person.user');

        // Enganchar aprobación con roles de Spatie
        $userModel = $rescuer->person?->user;
        if ($userModel) {
            if ($rescuer->aprobado === true) {
                $userModel->assignRole('rescatista');
            } elseif ($rescuer->aprobado === false || $rescuer->aprobado === null) {
                $userModel->removeRole('rescatista');
            }
        }

        // Enviar correo al ciudadano si cambió el estado de aprobación
        if ($oldApproved !== $rescuer->aprobado && $userModel && $userModel->email) {
            try {
                $approved = $rescuer->aprobado === true;
                Mail::to($userModel->email)->send(new RescuerApplicationResponse($rescuer, $approved));
            } catch (\Exception $e) {
                \Log::error('Error enviando correo de respuesta de solicitud de rescatista: ' . $e->getMessage());
            }
        }

        $message = $validated['action'] === 'approve' 
            ? 'La solicitud de rescatista ha sido aprobada correctamente.' 
            : 'La solicitud de rescatista ha sido rechazada correctamente.';

        return Redirect::route('rescuers.index')
            ->with('success', $message);
    }

    public function destroy($id): RedirectResponse
    {
        Rescuer::find($id)->delete();

        return Redirect::route('rescuers.index')
            ->with('success', 'Rescatista eliminado correctamente');
    }
}
