<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    // Créer app/Http/Middleware/SetLocale.php
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale(session('locale', config('app.locale')));
        return $next($request);
    }
}