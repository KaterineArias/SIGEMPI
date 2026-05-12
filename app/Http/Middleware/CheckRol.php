<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRol
{
    public function handle(Request $request, Closure $next, string $rol)
    {
        if (session('rol') !== $rol) {
            return redirect()->route('dashboard');
        }
        return $next($request);
    }
}