<?php
namespace App\Jobs;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Services\FCMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendResultNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public readonly Exam $exam) {}

    public function handle(FCMService $fcm): void
    {
        // Fetch all un-notified results with parent FCM token
        $results = ExamResult::with([
                'student.parentUser',
                'student.schoolClass',
                'exam.subject',
                'exam.examType',
            ])
            ->where('exam_id', $this->exam->id)
            ->whereNull('notified_at')
            ->get();

        $sent = 0;

        foreach ($results as $result) {
            $parent = $result->student?->parentUser;

            if (!$parent?->fcm_token) {
                // No token — still mark notified to avoid re-processing
                $result->update(['notified_at' => now()]);
                continue;
            }

            $exam    = $result->exam;
            $student = $result->student;

            // Build notification text
            if ($result->is_absent) {
                $body = "{$student->name} was absent for {$exam->subject->name} exam.";
            } else {
                $status = $result->is_pass ? '✅ Pass' : '❌ Fail';
                $body   = "{$exam->subject->name}: {$result->marks_obtained}/{$exam->max_marks} — Grade {$result->grade} {$status}";
            }

            $success = $fcm->send(
                token: $parent->fcm_token,
                title: "📝 {$exam->examType->name} Result — {$student->name}",
                body:  $body,
                data:  [
                    'type'        => 'exam_result',
                    'exam_id'     => (string) $exam->id,
                    'student_id'  => (string) $student->id,
                    'subject'     => $exam->subject->name,
                    'marks'       => (string) ($result->marks_obtained ?? 'AB'),
                    'max_marks'   => (string) $exam->max_marks,
                    'grade'       => $result->grade ?? 'AB',
                    'percentage'  => (string) ($result->percentage ?? '0'),
                    'is_pass'     => $result->is_pass ? 'true' : 'false',
                    'rank'        => (string) ($result->rank ?? '0'),
                    'exam_type'   => $exam->examType->code,
                    'class'       => $student->schoolClass->name,
                    'click_action'=> 'EXAM_RESULT_SCREEN',
                ]
            );

            if ($success) {
                $result->update(['notified_at' => now()]);
                $sent++;
            }
        }

        Log::info("Result notifications: {$sent} sent for exam #{$this->exam->id}");
    }
}