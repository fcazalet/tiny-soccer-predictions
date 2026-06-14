<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoLoginController extends Controller
{
    public function show()
    {
        return view('auth.demo-login');
    }

    public function loginAs(Request $request)
    {
        $request->validate(['role' => 'required|in:admin,player']);

        $email = $request->role === 'admin'
            ? config('app.demo_admin_email')
            : config('app.demo_player_email');

        $user = User::where('email', $email)->firstOrFail();

        Auth::login($user, remember: true);   // ← remember: true pour persister

        $request->session()->regenerate();    // ← indispensable après un login manuel
        $request->session()->save(); 

        return redirect()->intended('/dashboard');
    }
}
