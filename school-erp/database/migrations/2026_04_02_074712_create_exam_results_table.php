<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('exam_results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
        $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        $table->foreignId('entered_by')->constrained('users');   // teacher
        $table->decimal('marks_obtained', 5, 2)->nullable();   // null if absent
        $table->decimal('percentage', 5, 2)->nullable();
        $table->string('grade', 5)->nullable();             // A+, A, B+...
        $table->decimal('grade_point', 3, 2)->nullable();    // 10.0, 9.0...
        $table->boolean('is_absent')->default(false);
        $table->boolean('grace_applied')->default(false);
        $table->decimal('grace_marks', 4, 2)->default(0);
        $table->integer('rank')->nullable();
        $table->string('remarks', 200)->nullable();
        $table->timestamp('notified_at')->nullable();         // FCM sent time
        $table->timestamps();

        $table->unique(['exam_id', 'student_id']);
        $table->index('student_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
