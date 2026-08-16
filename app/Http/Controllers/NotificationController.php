<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->when($request->status === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($request->status === 'read', fn ($q) => $q->whereNotNull('read_at'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $unreadCount = auth()->user()->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function show(string $notification): RedirectResponse
    {
        $notification = $this->notification($notification);

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        if ($url = data_get($notification->data, 'url')) {
            return redirect($url);
        }

        return redirect()->route('admin.notifications.index');
    }

    public function read(string $notification): RedirectResponse
    {
        $this->notification($notification)->markAsRead();

        return redirect()->route('admin.notifications.index');
    }

    public function readAll(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->route('admin.notifications.index');
    }

    public function destroy(string $notification): RedirectResponse
    {
        $this->notification($notification)->delete();

        return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted.');
    }

    private function notification(string $id): DatabaseNotification
    {
        return auth()->user()->notifications()->findOrFail($id);
    }
}
