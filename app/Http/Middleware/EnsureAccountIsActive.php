<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Déconnecte immédiatement un utilisateur dont le compte a été désactivé,
 * même s'il avait déjà une session ouverte.
 */
class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $utilisateur = $request->user();

        if ($utilisateur !== null && ! $utilisateur->actif) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'identifiant' => 'Ce compte est désactivé. Contactez la bibliothèque.',
            ]);
        }

        return $next($request);
    }
}
