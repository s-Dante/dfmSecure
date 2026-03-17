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

class PasswordResetController extends Controller
{
    public function showVerifyEmail()
    {
        return view('auth.verify-email');
    }

    public function sendToken(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email'], [
            'email.exists' => 'Este correo no está registrado en nuestro sistema.',
        ]);
        
        // Generate a random 6 digit token
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Upsert into password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );
        
        // Send email
        Mail::to($request->email)->send(new SendResetTokenMail($token));
        
        // Store email temporarily in session instead of passing via back() redirect
        $request->session()->put('pending_reset_email', $request->email);
        return redirect()->route('verifyToken');
    }

    public function showVerifyToken(Request $request)
    {
        $email = $request->session()->get('pending_reset_email');
        if (!$email) {
            return redirect()->route('verifyEmail');
        }
        return view('auth.verify-token', compact('email'));
    }

    public function verifyToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|numeric|digits:6',
        ]);
        
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();
            
        if (!$record) {
            return back()->withErrors(['token' => 'El código proporcionado es incorrecto.'])->withInput();
        }
        
        $request->session()->put('reset_email', $request->email);
        $request->session()->put('token_verified', true);
        
        return redirect()->route('resetPassword');
    }

    public function showResetPassword(Request $request)
    {
        if (!$request->session()->get('token_verified')) {
            return redirect()->route('verifyEmail');
        }
        return view('auth.reset-pswd');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
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
            'password.regex' => 'La contraseña debe contener mayúsculas, minúsculas, números y SOLO los siguientes caracteres especiales: / - _ * & ( ).',
        ]);
        
        $email = $request->session()->get('reset_email');
        if (!$email || !$request->session()->get('token_verified')) {
            return redirect()->route('verifyEmail');
        }
        
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update(['password' => Hash::make($request->password)]);
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            $request->session()->forget(['reset_email', 'token_verified', 'pending_reset_email']);
            Auth::login($user);
            return redirect()->route('dashboard');
        }
        
        return redirect()->route('verifyEmail')->withErrors(['email' => 'Usuario no encontrado']);
    }
}
