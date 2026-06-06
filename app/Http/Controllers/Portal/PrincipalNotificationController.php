<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class PrincipalNotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('principal.notifications', compact('notifications'));
    }

    public function markAsRead(string $id)
    {
        $notification = DatabaseNotification::findOrFail($id);

        // Ensure it belongs to the authenticated user
        abort_unless(
            $notification->notifiable_id === auth()->id() &&
            $notification->notifiable_type === get_class(auth()->user()),
            403
        );

        $notification->markAsRead();

        return back()->with('success', __('notification_marked_read'));
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', __('all_notifications_marked_read'));
    }
}