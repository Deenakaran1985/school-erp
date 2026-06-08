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
    Schema::create('exams', function (Blueprint $table) {
        $table->id();
        $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
        $table->foreignId('exam_type_id')->constrained('exam_types');
        $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
        $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
        $table->foreignId('created_by')->constrained('users');
        $table->string('exam_name', 150);       // "Unit Test 1 - Maths - Class VI"
        $table->date('exam_date');
        $table->time('start_time')->nullable();
        $table->integer('duration_minutes')->nullable();
        $table->integer('max_marks')->default(100);
        $table->integer('pass_marks')->default(35);
        $table->string('hall_no', 30)->nullable();
        $table->enum('status', [
            'scheduled',    // created, visible to students
            'ongoing',      // exam day
            'marks_entry',  // teacher entering marks
            'published',    // results live — FCM triggered
            'cancelled'
        ])->default('scheduled');
        $table->timestamp('published_at')->nullable();
        $table->text('instructions')->nullable();
        $table->timestamps();

        $table->index(['school_class_id', 'exam_date']);
        $table->index(['academic_year_id', 'exam_type_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
