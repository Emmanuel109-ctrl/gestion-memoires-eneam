<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()->paginate(15),
            'nonLues' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function lire(Request $request, string $notification): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('dashboard'));
    }

    public function toutLire(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Toutes vos notifications sont maintenant lues.');
    }
}
