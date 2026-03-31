<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\SendResetTokenMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\UsesDbObjects;

class PasswordResetController extends Controller
{
    use UsesDbObjects;

    // ─────────────────────────────────────────────────────────────────────────
    //  VISTAS
    // ─────────────────────────────────────────────────────────────────────────

    public function showVerifyEmail()
    {
        return view('auth.verify-email');
    }

    public function showVerifyToken(Request $request)
    {
        $email = $request->session()->get('pending_reset_email');
        if (!$email) {
            return redirect()->route('verifyEmail');
        }
        return view('auth.verify-token', compact('email'));
    }

    public function showResetPassword(Request $request)
    {
        if (!$request->session()->get('token_verified')) {
            return redirect()->route('verifyEmail');
        }
        return view('auth.reset-pswd');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PASO 1: Enviar token de recuperación
    //  → Eloquent/DB::table: updateOrInsert() nativo.
    //  → SP mode: llama sp_send_reset_token que valida el email y hace upsert.
    // ─────────────────────────────────────────────────────────────────────────

    public function sendToken(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email'], [
            'email.exists' => 'Este correo no está registrado en nuestro sistema.',
        ]);

        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        if ($this->useDbObjects()) {
            // El SP valida el email y hace el upsert en password_reset_tokens
            $this->execProcedure('sp_send_reset_token', [$request->email, $token]);
        } else {
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                ['token' => $token, 'created_at' => Carbon::now()]
            );
        }

        // El envío de email es siempre en PHP (no tiene sentido en un SP)
        Mail::to($request->email)->send(new SendResetTokenMail($token));

        $request->session()->put('pending_reset_email', $request->email);
        return redirect()->route('verifyToken');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PASO 2: Verificar token
    //  → Eloquent/DB::table: consulta directa a password_reset_tokens.
    //  → SP mode: llama sp_verify_reset_token.
    // ─────────────────────────────────────────────────────────────────────────

    public function verifyToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|numeric|digits:6',
        ]);

        if ($this->useDbObjects()) {
            $results = $this->callProcedure('sp_verify_reset_token', [
                $request->email,
                $request->token,
            ]);
            $record = $results[0] ?? null;
        } else {
            $record = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->first();
        }

        if (!$record) {
            return back()->withErrors(['token' => 'El código proporcionado es incorrecto.'])->withInput();
        }

        $request->session()->put('reset_email', $request->email);
        $request->session()->put('token_verified', true);

        return redirect()->route('resetPassword');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PASO 3: Cambiar contraseña
    //  → Eloquent: User::where()->first() + $user->update().
    //  → SP mode: llama sp_change_password pasando el hash generado en PHP.
    // ─────────────────────────────────────────────────────────────────────────

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required', 'string', 'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[\/\-\_\*\&\(\)]/',
                'regex:/^[a-zA-Z0-9\/\-\_\*\&\(\)]+$/',
                'confirmed',
            ],
        ], [
            'password.regex' => 'La contraseña debe contener mayúsculas, minúsculas, números y SOLO los siguientes caracteres especiales: / - _ * & ( ).',
        ]);

        $email = $request->session()->get('reset_email');
        if (!$email || !$request->session()->get('token_verified')) {
            return redirect()->route('verifyEmail');
        }

        $passwordHash = Hash::make($request->password);

        if ($this->useDbObjects()) {
            // SP actualiza la contraseña y elimina el token en una sola transacción
            $this->callProcedure('sp_change_password', [$email, $passwordHash]);

            $request->session()->forget(['reset_email', 'token_verified', 'pending_reset_email']);

            // Cargar el usuario para hacer login automático
            $user = User::where('email', $email)->first();
            if ($user) {
                Auth::login($user);
                return redirect()->route('dashboard');
            }

            return redirect()->route('logIn')->with('success', 'Contraseña actualizada. Por favor inicia sesión.');
        }

        // Modo Eloquent
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update(['password' => $passwordHash]);
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            $request->session()->forget(['reset_email', 'token_verified', 'pending_reset_email']);
            Auth::login($user);
            return redirect()->route('dashboard');
        }

        return redirect()->route('verifyEmail')->withErrors(['email' => 'Usuario no encontrado']);
    }
}
