<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginToken;
use App\Models\User;
use App\Notifications\LoginOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    // Étape 1 : afficher le formulaire email
    public function showEmailForm()
    {
        return view('auth.email');
    }

    // Étape 2 : envoyer le code OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');

        // Créer ou récupérer l'utilisateur
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => explode('@', $email)[0]]
        );

        // Supprimer les anciens tokens
        LoginToken::where('email', $email)->delete();

        // Générer un code à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        LoginToken::create([
            'email'      => $email,
            'token'      => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Envoyer le mail
        $user->notify(new LoginOtpNotification($code));

        return redirect()->route('auth.otp.form', ['email' => $email]);
    }

    // Étape 3 : afficher le formulaire de saisie du code
    public function showOtpForm(Request $request)
    {
        return view('auth.otp', ['email' => $request->query('email')]);
    }

    // Étape 4 : vérifier le code
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|digits:6',
        ]);

        $loginToken = LoginToken::where('email', $request->email)
            ->where('token', $request->token)
            ->where('expires_at', '>', now())
            ->first();

        if (! $loginToken) {
            return back()->withErrors(['token' => 'Code invalide ou expiré.']);
        }

        // Supprimer le token utilisé
        $loginToken->delete();

        // Connecter l'utilisateur
        $user = User::where('email', $request->email)->firstOrFail();
        Auth::login($user, remember: true);

        return redirect()->intended('/dashboard');
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
