<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Jobs\SendResultNotification;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    // ── View results sheet ─────────────────────────────────
    public function show(Exam $exam)
    {
        $this->authorize('exam.results.view');

        $exam->load(['examType', 'subject', 'schoolClass']);

        $results = ExamResult::with(['student.section'])
            ->where('exam_id', $exam->id)
            ->orderBy('rank')
            ->get();

        // Summary stats
        $summary = [
            'total'    => $results->count(),
            'appeared' => $results->where('is_absent', false)->count(),
            'absent'   => $results->where('is_absent', true)->count(),
            'passed'   => $results->filter(fn($r) => $r->is_pass)->count(),
            'failed'   => $results->filter(fn($r) => !$r->is_absent && !$r->is_pass)->count(),
            'average'  => round($results->whereNotNull('marks_obtained')->avg('marks_obtained'), 1),
            'highest'  => $results->max('marks_obtained'),
            'lowest'   => $results->whereNotNull('marks_obtained')->min('marks_obtained'),
        ];

        $summary['pass_percent'] = $summary['appeared'] > 0
            ? round(($summary['passed'] / $summary['appeared']) * 100, 1)
            : 0;

        // Grade distribution
        $gradeDistribution = $results
            ->whereNotNull('grade')
            ->groupBy('grade')
            ->map->count();

        return view('exams.results', compact(
            'exam', 'results', 'summary', 'gradeDistribution'
        ));
    }

    // ── Publish results + dispatch FCM job ─────────────────
    public function publish(Request $request, Exam $exam)
    {
        $this->authorize('exam.results.publish');

        if ($exam->status === 'published') {
            return back()->with('error', 'Results already published.');
        }

        if (!ExamResult::where('exam_id', $exam->id)->exists()) {
            return back()->with('error', 'No marks entered yet. Enter marks before publishing.');
        }

        // Mark exam as published
        $exam->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        // Dispatch FCM notification job to queue
        SendResultNotification::dispatch($exam)
            ->onQueue('notifications');

        return redirect()
            ->route('admin.exams.results', $exam)
            ->with('success', 'Results published! Push notifications queued for parents.');
    }

    // ── Re-send notifications (for missed devices) ─────────
    public function resendNotifications(Exam $exam)
    {
        $this->authorize('exam.results.publish');

        // Reset notified_at so job re-sends to all
        ExamResult::where('exam_id', $exam->id)
            ->update(['notified_at' => null]);

        SendResultNotification::dispatch($exam)->onQueue('notifications');

        return back()->with('success', 'Re-sending notifications to all parents.');
    }
}