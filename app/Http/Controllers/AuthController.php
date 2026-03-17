<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Enums\RoleEnum;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.signin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:30|unique:users',
            'father_lastname' => 'required|string|max:255',
            'mother_lastname' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
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
                'confirmed'
            ],
        ], [
            'birth_date.before_or_equal' => 'Debes ser mayor de 18 años para registrarte.',
            'password.regex' => 'La contraseña debe contener mayúsculas, minúsculas, números y SOLO los siguientes caracteres especiales: / - _ * & ( ).',
        ]);

        // Fetch the default role ID for 'insured'
        $role = Role::where('name', RoleEnum::INSURED->value)->first();
        if (!$role) {
            return back()->with('error', 'Rol predeterminado no encontrado. Por favor, contacta al administrador.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'father_lastname' => $validated['father_lastname'],
            'mother_lastname' => $validated['mother_lastname'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'birth_date' => $validated['birth_date'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
