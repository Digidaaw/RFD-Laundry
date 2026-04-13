<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Jika role adalah admin, izinkan semua
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Jika role adalah kasir, cek apakah route adalah kasir
        if ($user->role === 'kasir') {
            $route = $request->route();
            
            // Cek apakah route name dimulai dengan 'kasir'
            if ($route && strpos($route->getName(), 'kasir') === 0) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini');
            }
            
            return $next($request);
        }

        // Jika role tidak dikenal, tolak
        abort(403, 'Unauthorized');
    }
}