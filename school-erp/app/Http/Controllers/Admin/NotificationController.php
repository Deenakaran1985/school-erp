<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\SchoolNotification;
use App\Models\Student;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private FCMService $fcm) {}

    public function index()
    {
        $this->authorize('notification.view');

        $notifications = SchoolNotification::with('sentBy')
            ->latest()
            ->paginate(20);

        $classes = SchoolClass::active()->get();

        return view('notifications.index', compact('notifications', 'classes'));
    }

    public function send(Request $request)
    {
        $this->authorize('notification.send');

        $validated = $request->validate([
            'title'           => 'required|string|max:200',
            'body'            => 'required|string',
            'target_role'     => 'required|in:all,parent,student,teacher',
            'target_class_id' => 'nullable|exists:school_classes,id',
            'type'            => 'nullable|string|max:30',
        ]);

        // Build FCM token recipient list
        $query = User::whereNotNull('fcm_token')->where('status', 'active');

        if ($validated['target_role'] !== 'all') {
            $query->role($validated['target_role']);
        }

        // Filter by class if provided (get parents of students in that class)
        if (!empty($validated['target_class_id'])) {
            $parentIds = Student::where('school_class_id', $validated['target_class_id'])
                ->whereNotNull('parent_user_id')
                ->pluck('parent_user_id');
            $query->whereIn('id', $parentIds);
        }

        $tokens = $query->pluck('fcm_token')->filter()->toArray();

        // Send FCM
        $result = $this->fcm->sendBatch(
            $tokens,
            $validated['title'],
            $validated['body'],
            ['type' => $validated['type'] ?? 'general']
        );

        // Save to DB
        SchoolNotification::create([
            'sent_by'         => auth()->id(),
            'title'           => $validated['title'],
            'body'            => $validated['body'],
            'type'            => $validated['type'] ?? 'general',
            'target_role'     => $validated['target_role'],
            'target_class_id' => $validated['target_class_id'] ?? null,
            'sent_count'      => $result['sent'],
            'sent_at'         => now(),
        ]);

        return back()->with('success',
            "Notification sent to {$result['sent']} devices. Failed: {$result['failed']}."
        );
    }
}