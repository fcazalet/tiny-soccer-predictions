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

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }