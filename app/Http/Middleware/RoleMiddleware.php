<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôle d'accès par rôle.
 *
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,bibliothecaire')
 *
 * L'utilisateur doit posséder AU MOINS UN des rôles listés.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $utilisateur = $request->user();

        if ($utilisateur === null) {
            return redirect()->route('login');
        }

        if ($roles === [] || in_array($utilisateur->role, $roles, true)) {
            return $next($request);
        }

        abort(403, "Accès refusé : cette page est réservée au profil "
            .implode(' ou ', $roles).'.');
    }
}
