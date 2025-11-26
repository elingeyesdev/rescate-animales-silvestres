<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Person;
use App\Services\Api\User\UserRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    public function __construct(
        private readonly UserRegistrationService $userRegistrationService,
    ) {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nombre'   => 'required|string',
            'ci'       => 'required|string',
            'telefono' => 'required|string',
            'es_cuidador' => 'nullable|boolean'
        ]);

        // Lógica de creación delegada al servicio
        $this->userRegistrationService->register($validated);

        return response()->json([
            'message' => 'Usuario registrado correctamente',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $user->load('person');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'nombre'   => 'sometimes|string',
            'ci'       => 'sometimes|string',
            'telefono' => 'sometimes|string',
            'es_cuidador' => 'sometimes|boolean'
        ]);

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // actualizar person
        if ($user->person) {
            $user->person->update($request->only('nombre','ci','telefono','es_cuidador'));
        }

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user'    => $user->load('person')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
