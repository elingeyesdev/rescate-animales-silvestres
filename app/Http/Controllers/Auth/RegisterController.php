<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Person;
use App\Models\Report;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'ci' => ['required', 'string', 'max:50'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar :max caracteres.',
            'ci.required' => 'El documento (CI) es obligatorio.',
            'ci.max' => 'El CI no puede superar :max caracteres.',
            'telefono.max' => 'El teléfono no puede superar :max caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Person::create([
            'usuario_id' => $user->id,
            'nombre' => $data['nombre'],
            'ci' => $data['ci'],
            'telefono' => $data['telefono'] ?? null,
            'es_cuidador' => false,
        ]);

        // Rol por defecto: ciudadano (se asegura que exista aunque el seeder no se haya ejecutado)
        if (method_exists($user, 'assignRole')) {
            $role = Role::firstOrCreate(['name' => 'ciudadano', 'guard_name' => 'web']);
            $user->assignRole($role);
        }

        return $user;
    }

    /**
     * The user has been registered.
     * Logout immediately and send to login page to start session explicitly.
     * Guardar el reporte pendiente en sesión para asociarlo después del login.
     */
    protected function registered(Request $request, $user)
    {
        // Guardar el reporte pendiente en sesión antes de hacer logout
        $reportId = session('pending_report_id');
        if ($reportId) {
            $request->session()->put('pending_report_id', $reportId);
        }
        
        $this->guard()->logout();
        return redirect('/login')
            ->with('info', 'Registro exitoso. Por favor inicia sesión para asociar tu reporte.');
    }
}
