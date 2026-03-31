<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Enums\RoleEnum;
use App\Traits\UsesDBObjects;

class AuthController extends Controller
{
    use UsesDBObjects;

    // ─────────────────────────────────────────────────────────────────────────
    //  VISTAS
    // ─────────────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.signin');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  LOGIN
    //  → Eloquent: usa Auth::attempt() nativo de Laravel.
    //  → SP mode:  busca el usuario con sp_find_user_by_email, valida el hash
    //              en PHP y hace Auth::login() con el modelo Eloquent.
    //              Las sesiones siguen siendo manejadas 100% por Laravel.
    // ─────────────────────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($this->useDBObjects()) {
            return $this->loginWithSP($request, $credentials);
        }

        return $this->loginWithEloquent($request, $credentials);
    }

    private function loginWithEloquent(Request $request, array $credentials): \Illuminate\Http\RedirectResponse
    {
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    private function loginWithSP(Request $request, array $credentials): \Illuminate\Http\RedirectResponse
    {
        // Llama sp_find_user_by_email para traer el usuario de la BD
        $results = $this->callProcedure('sp_find_user_by_email', [$credentials['email']]);

        if (empty($results)) {
            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->onlyInput('email');
        }

        $userData = (array) $results[0];

        // Validar contraseña en PHP (Hash::check) — el SP no expone el hash directamente al cliente
        if (!Hash::check($credentials['password'], $userData['password'])) {
            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->onlyInput('email');
        }

        // Cargar el modelo Eloquent para que Auth::login() funcione correctamente
        $user = User::find($userData['id']);
        if (!$user) {
            return back()->withErrors(['email' => 'Usuario no encontrado.'])->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  REGISTRO
    //  → Eloquent: User::create() nativo.
    //  → SP mode:  llama sp_register_user pasando el hash generado en PHP.
    // ─────────────────────────────────────────────────────────────────────────

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'username' => 'required|string|max:30|unique:users',
            'father_lastname' => 'required|string|max:50',
            'mother_lastname' => 'nullable|string|max:50',
            'email' => 'required|string|email|max:80|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'birth_date' => 'required|date|before_or_equal:-18 years',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[\/\-\_\*\&\(\)]/',
                'regex:/^[a-zA-Z0-9\/\-\_\*\&\(\)]+$/',
                'confirmed',
            ],
        ], [
            'birth_date.before_or_equal' => 'Debes ser mayor de 18 años para registrarte.',
            'password.regex' => 'La contraseña debe contener mayúsculas, minúsculas, números y SOLO los siguientes caracteres especiales: / - _ * & ( ).',
        ]);

        // Obtener el rol 'insured' (necesario en ambos modos)
        $role = Role::where('name', RoleEnum::INSURED->value)->first();
        if (!$role) {
            return back()->with('error', 'Rol predeterminado no encontrado. Contacta al administrador.');
        }

        if ($this->useDBObjects()) {
            return $this->registerWithSP($request, $validated, $role->id);
        }

        return $this->registerWithEloquent($request, $validated, $role->id);
    }

    private function registerWithEloquent(Request $request, array $validated, int $roleId): \Illuminate\Http\RedirectResponse
    {
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'father_lastname' => $validated['father_lastname'],
            'mother_lastname' => $validated['mother_lastname'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'birth_date' => $validated['birth_date'],
            'password' => Hash::make($validated['password']),
            'role_id' => $roleId,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    private function registerWithSP(Request $request, array $validated, int $roleId): \Illuminate\Http\RedirectResponse
    {
        // El hash se genera en PHP — el SP recibe el hash, nunca la contraseña en texto plano
        $passwordHash = Hash::make($validated['password']);

        $results = $this->callProcedure('sp_register_user', [
            $validated['name'],
            $validated['father_lastname'],
            $validated['mother_lastname'] ?? null,
            $validated['username'],
            $validated['email'],
            $validated['phone'],
            $validated['birth_date'],
            $passwordHash,
            'other',   // género por defecto (el formulario de registro no pregunta esto aún)
            $roleId,
        ]);

        if (empty($results)) {
            return back()->with('error', 'No se pudo completar el registro. Intenta de nuevo.');
        }

        $newUserId = $results[0]->id ?? null;
        if (!$newUserId) {
            return back()->with('error', 'No se pudo completar el registro. Intenta de nuevo.');
        }

        // Cargamos el modelo para que Auth::login() funcione con sesiones de Laravel
        $user = User::find($newUserId);
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  LOGOUT — igual en ambos modos (manejo 100% de sesiones Laravel)
    // ─────────────────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
