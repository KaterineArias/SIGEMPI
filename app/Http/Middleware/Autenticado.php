<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Autenticado
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('usuario')) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}