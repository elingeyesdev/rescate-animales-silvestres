<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Rescuer;
use App\Models\Veterinarian;
use App\Models\Center;
use App\Models\User;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Mail\NewRescuerApplicationNotification;
use App\Mail\NewVeterinarianApplicationNotification;
use App\Mail\CaregiverCommitmentConfirmation;
use App\Rules\NotWebpImage;
use App\Services\User\UserTrackingService;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra la pantalla de perfil del usuario autenticado.
     */
    public function index()
    {
        $user = Auth::user();

        $person = $user->person;
        if ($person) {
            $person->load('cuidadorCenter');
        } else {
            $person = new Person([
                'nombre' => '',
                'ci' => '',
                'telefono' => '',
                'es_cuidador' => false,
            ]);
        }

        $rescuer = $person->exists ? $person->rescuers()->latest()->first() : null;
        $veterinarian = $person->exists ? $person->veterinarians()->latest()->first() : null;
        
        // Obtener centros para el mapa (solo si es_cuidador es true o está marcando el checkbox)
        $centers = Center::orderBy('nombre')->get(['id', 'nombre', 'latitud', 'longitud']);

        // Si es admin o encargado, obtener los mensajes de contacto
        $contactMessages = null;
        if ($user->hasAnyRole(['admin', 'encargado'])) {
            $contactMessages = ContactMessage::with('user.person', 'leidoPor')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('profile.index', compact('user', 'person', 'rescuer', 'veterinarian', 'centers', 'contactMessages'));
    }

    /**
     * Actualiza la información de perfil del usuario autenticado.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'modo'      => 'nullable|in:datos,cuidador,rescatista,veterinario',
            // Datos básicos de persona
            'nombre'   => 'required|string|max:255',
            'ci'       => 'required|string|max:255',
            'telefono' => 'nullable|string|max:255',

            // Foto de la persona
            'foto'                => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120', new \App\Rules\NotWebpImage()],

            // Cuidador voluntario
            'es_cuidador'         => 'nullable|boolean',
            // Es opcional: si viene y está marcado, se toma como true; si viene desmarcado o no viene, es false.
            'compromiso_cuidador' => 'nullable|boolean',
            'cuidador_center_id'  => 'nullable|integer|exists:centers,id',

            // Rol de colaboración (rescatista / veterinario)
            'rol_postulacion'     => 'nullable|in:rescatista,veterinario',

            // Veterinario
            'especialidad'        => 'nullable|string|max:255',

            // CV y motivo (para rescatista o veterinario)
            'cv'                  => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120', new \App\Rules\NotWebpImage()],
            'motivo_postulacion'  => 'nullable|string',
        ]);

        // Upsert de Person vinculada al usuario
        $person = $user->person;
        $oldPersonData = null;
        if (!$person) {
            $person = new Person();
            $person->usuario_id = $user->id;
        } else {
            // Guardar valores antiguos para tracking (incluyendo todos los campos relevantes)
            $oldPersonData = [
                'nombre' => $person->nombre,
                'ci' => $person->ci,
                'telefono' => $person->telefono,
                'es_cuidador' => $person->es_cuidador,
                'foto_path' => $person->foto_path,
                'cuidador_center_id' => $person->cuidador_center_id,
            ];
        }

        $person->nombre = $validated['nombre'];
        $person->ci = $validated['ci'];
        $person->telefono = $validated['telefono'] ?? null;

        $modo = $validated['modo'] ?? 'datos';

        // Solo actualizamos es_cuidador cuando el usuario usa la sección de cuidador
        if ($modo === 'cuidador') {
            $wasCuidador = (int)$person->es_cuidador === 1;
            $person->es_cuidador = $request->boolean('compromiso_cuidador');
            $isNewCommitment = !$wasCuidador && $person->es_cuidador;
            
            // Registrar tracking de solicitud de cuidador
            if ($isNewCommitment) {
                try {
                    app(UserTrackingService::class)->logApplication('caregiver', $person, $user->id);
                } catch (\Exception $e) {
                    //
                }
            }
            
            // Si el usuario se desmarca como cuidador, remover el rol y limpiar campos de aprobación
            if (!$person->es_cuidador) {
                $user->removeRole('cuidador');
                $person->cuidador_aprobado = null;
                $person->cuidador_motivo_revision = null;
                $person->cuidador_center_id = null;
            } else {
                // Si se marca como cuidador, guardar el centro seleccionado
                // IMPORTANTE: NO se asigna el rol aquí. El rol solo se asignará cuando
                // un admin/encargado complete el campo cuidador_motivo_revision en PersonController@update
                if ($request->filled('cuidador_center_id')) {
                    $person->cuidador_center_id = $request->input('cuidador_center_id');
                }
                // Asegurarse de que el rol NO se asigne automáticamente
                // Si por alguna razón ya tenía el rol, lo removemos hasta que sea aprobado
                if ($user->hasRole('cuidador') && empty($person->cuidador_motivo_revision)) {
                    $user->removeRole('cuidador');
                }
            }
        }

        // Guardar foto si viene adjunta
        $oldFotoPath = $person->foto_path;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('personas', 'public');
            $person->foto_path = $path;
        }
        $person->save();

        // Registrar tracking de actualización de perfil (incluyendo todos los campos relevantes)
        try {
            $newPersonData = [
                'nombre' => $person->nombre,
                'ci' => $person->ci,
                'telefono' => $person->telefono,
                'es_cuidador' => $person->es_cuidador,
                'foto_path' => $person->foto_path,
                'cuidador_center_id' => $person->cuidador_center_id,
            ];
            app(UserTrackingService::class)->logProfileUpdate($person, $oldPersonData, $newPersonData);
        } catch (\Exception $e) {
            //
        }

        // NO asignamos el rol automáticamente aquí
        // El rol se asignará solo cuando un admin/encargado complete el motivo_revision
        // en PersonController@update

        // Determinar rol según el modo seleccionado en "Quiero colaborar"
        $rol = 'ninguno';
        if ($modo === 'rescatista') {
            $rol = 'rescatista';
        } elseif ($modo === 'veterinario') {
            $rol = 'veterinario';
        }

        // Si no elige rol, no modificamos tablas de rescatista/veterinario
        if ($rol === 'rescatista') {
            $isNew = !Rescuer::where('persona_id', $person->id)->exists();
            $rescuer = Rescuer::firstOrNew(['persona_id' => $person->id]);
            $rescuer->persona_id = $person->id;

            // Guardar CV si viene adjunto
            if ($request->hasFile('cv')) {
                $path = $request->file('cv')->store('cv', 'public');
                $rescuer->cv_documentado = $path;
            }

            if (!empty($validated['motivo_postulacion'])) {
                $rescuer->motivo_postulacion = $validated['motivo_postulacion'];
            }

            // Al actualizar desde el perfil, reiniciamos la aprobación para revisión
            $rescuer->aprobado = null;
            $rescuer->motivo_revision = null;

            $rescuer->save();
            $rescuer->load('person.user');

            // Registrar tracking de solicitud desde perfil
            try {
                app(UserTrackingService::class)->logApplication('rescuer', $rescuer, $user->id);
            } catch (\Exception $e) {
                //
            }

            // Enviar correo a encargados y administradores si es una nueva solicitud
            if ($isNew) {
                $adminsAndEncargados = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'encargado']);
                })->get();

                foreach ($adminsAndEncargados as $adminUser) {
                    try {
                        Mail::to($adminUser->email)->send(new NewRescuerApplicationNotification($rescuer));
                    } catch (\Exception $e) {
                        //
                    }
                }
            }
        } elseif ($rol === 'veterinario') {
            $isNew = !Veterinarian::where('persona_id', $person->id)->exists();
            $veterinarian = Veterinarian::firstOrNew(['persona_id' => $person->id]);
            $veterinarian->persona_id = $person->id;
            $veterinarian->especialidad = $validated['especialidad'] ?? null;

            if ($request->hasFile('cv')) {
                $path = $request->file('cv')->store('cv', 'public');
                $veterinarian->cv_documentado = $path;
            }

            if (!empty($validated['motivo_postulacion'])) {
                $veterinarian->motivo_postulacion = $validated['motivo_postulacion'];
            }

            $veterinarian->aprobado = null;
            $veterinarian->motivo_revision = null;

            $veterinarian->save();
            $veterinarian->load('person.user');

            // Registrar tracking de solicitud desde perfil
            try {
                app(UserTrackingService::class)->logApplication('veterinarian', $veterinarian, $user->id);
            } catch (\Exception $e) {
                //
            }

            // Enviar correo a encargados y administradores si es una nueva solicitud
            if ($isNew) {
                $adminsAndEncargados = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'encargado']);
                })->get();

                foreach ($adminsAndEncargados as $adminUser) {
                    try {
                        Mail::to($adminUser->email)->send(new NewVeterinarianApplicationNotification($veterinarian));
                    } catch (\Exception $e) {
                        //
                    }
                }
            }
        }

        // Enviar correo de confirmación cuando se compromete como cuidador (solo si es nuevo compromiso)
        if ($modo === 'cuidador' && isset($isNewCommitment) && $isNewCommitment) {
            $center = $person->cuidador_center_id 
                ? Center::find($person->cuidador_center_id) 
                : null;
            
            if ($person->user && $person->user->email) {
                try {
                    Mail::to($person->user->email)->send(new CaregiverCommitmentConfirmation($person, $center));
                } catch (\Exception $e) {
                    //
                }
            }
        }

        return redirect()
            ->route('profile.index')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}


