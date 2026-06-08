<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NotificationRead;
use App\Models\SchoolNotification;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    // GET /api/notifications — list for this user's role
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->getRoleNames()->first() ?? 'parent';

        $notifications = SchoolNotification::forUser($user->id, $role)
            ->latest('sent_at')
            ->paginate(20);

        // Mark which ones this user has read
        $readIds = NotificationRead::where('user_id', $user->id)
            ->pluck('notification_id')
            ->toArray();

        $unreadCount = $notifications->filter(
            fn($n) => !in_array($n->id, $readIds)
        )->count();

        return response()->json([
            'success'      => true,
            'unread_count' => $unreadCount,
            'data' => $notifications->map(fn($n) => [
                'id'       => $n->id,
                'title'    => $n->title,
                'body'     => $n->body,
                'type'     => $n->type,
                'data'     => $n->data,
                'sent_at'  => $n->sent_at?->toIso8601String(),
                'is_read'  => in_array($n->id, $readIds),
            ]),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
            ],
        ]);
    }

    // POST /api/notifications/{id}/read
    public function markRead(Request $request, int $id)
    {
        NotificationRead::firstOrCreate([
            'notification_id' => $id,
            'user_id'         => $request->user()->id,
        ], [
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    // POST /api/profile/update-fcm — Flutter registers device token
    public function updateFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);
        $request->user()->update(['fcm_token' => $request->fcm_token]);
        return response()->json(['success' => true']);
    }
}