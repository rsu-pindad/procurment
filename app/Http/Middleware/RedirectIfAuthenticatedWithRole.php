<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticatedWithRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
            $user = Auth::user();
            $currentRoute = $request->route()?->getName();
            if ($user->hasRole('admin') && $currentRoute !== 'dashboard') {
                return redirect()->route('dashboard');
            }
            if ($user->hasRole('pengadaan|pegawai') && $currentRoute !== 'dashboard') {
                return redirect()->route('dashboard');
            }
            // if ($user->hasRole('admin') && $currentRoute !== 'dashboard') {
            // return redirect('/dashboard');
            // }
            // if ($user->hasRole('monitor') && $currentRoute !== 'monitor') {
            // return redirect('/monitor');
            // }
            // if ($user->hasRole('pengadaan') && $currentRoute !== 'pengadaan') {
            // return redirect('/dashboard');
            // return redirect()->route('dashboard');
            // }
            // return redirect('/select-unit');
            return abort(404);
        }

        return $next($request);
    }
}
