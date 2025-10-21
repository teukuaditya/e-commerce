<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user yang sedang login adalah admin
        if (auth()->user() && auth()->user()->role !== 'admin') {

            return redirect('/')->with('error', 'You do not have admin access.');
        }

        return $next($request);
    }
}
