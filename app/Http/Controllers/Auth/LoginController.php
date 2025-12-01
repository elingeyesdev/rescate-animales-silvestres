<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Report;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request): void
    {
        $messages = [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
        ];

        $request->validate([
            $this->username() => 'required|string|email',
            'password' => 'required|string|min:8',
        ], $messages);
    }

    /**
     * Return a failed login response with a mensaje claro.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => ['Las credenciales ingresadas no son válidas. Verifica tu correo y contraseña.'],
        ]);
    }

    /**
     * The user has been authenticated.
     * Asociar reporte pendiente si existe.
     */
    protected function authenticated(Request $request, $user)
    {
        $reportId = session('pending_report_id') ?? $request->session()->get('pending_report_id');
        
        if ($reportId) {
            $report = Report::find($reportId);
            if ($report && !$report->persona_id) {
                $personId = Person::where('usuario_id', $user->id)->value('id');
                if ($personId) {
                    $report->persona_id = $personId;
                    $report->save();
                    session()->forget('pending_report_id');
                    return redirect()->route('profile.index')
                        ->with('success', '¡Bienvenido! Tu hallazgo ha sido asociado a tu cuenta. Puedes verlo en tu perfil.');
                }
            }
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     * Redirigir a landing después de cerrar sesión.
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
