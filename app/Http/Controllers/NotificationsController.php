<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Notifications\NuevaSolicitudInstitucion;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // Eager load para optimizar
        $notifications = $user->notifications()->latest()->get();
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            $redirectUrl = $this->getRedirectUrlForNotification($notification);
            return response()->json(['message' => 'Notification marked as read.', 'redirect_url' => $redirectUrl]);
        }

        return response()->json(['error' => 'Notification not found.'], 404);
    }

    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function destroy(Request $request, $notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Notification deleted.']);
        }

        return response()->json(['error' => 'Notification not found.'], 404);
    }

    private function getRedirectUrlForNotification($notification): ?string
    {
        switch ($notification->type) {
            case NuevaSolicitudInstitucion::class:
                return route('solicitudes.gestionar');
            // Agrega otros casos según sea necesario
            default:
                return null;
        }
    }
}
