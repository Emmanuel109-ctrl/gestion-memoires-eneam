<?php

namespace App\Http\Controllers;

use App\Models\Memoire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriController extends Controller
{
    public function index(Request $request): View
    {
        return view('favoris.index', [
            'memoires' => $request->user()
                ->favoris()
                ->with(['filiere', 'annee'])
                ->orderByDesc('favoris.created_at')
                ->paginate(12),
        ]);
    }

    /**
     * Ajoute ou retire un mémoire de la sélection personnelle.
     */
    public function toggle(Request $request, Memoire $memoire): RedirectResponse
    {
        $this->authorize('view', $memoire);

        $resultat = $request->user()->favoris()->toggle($memoire->id);
        $ajoute = $resultat['attached'] !== [];

        return back()->with(
            'success',
            $ajoute
                ? 'Ce mémoire a été ajouté à vos favoris.'
                : 'Ce mémoire a été retiré de vos favoris.'
        );
    }
}
