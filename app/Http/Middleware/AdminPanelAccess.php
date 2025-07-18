<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminPanelAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'FORBIDDEN');
        }

        if ($user->hasRole('personal')) {
            return redirect('/personal');
        }
        
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return $next($request);   
        }

        abort(403, 'FORBIDDEN');
    }
}