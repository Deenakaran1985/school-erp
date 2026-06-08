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
    Schema::create('homework', function (Blueprint $table) {
        $table->id();
        $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
        $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
        $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
        $table->foreignId('assigned_by')->constrained('users');  // teacher
        $table->string('title', 200);
        $table->text('description')->nullable();
        $table->string('attachment')->nullable();     // file path
        $table->date('assigned_date');
        $table->date('due_date');
        $table->enum('status', ['active','closed'])->default('active');
        $table->timestamps();
        $table->index(['school_class_id', 'due_date']);
    });

    // Homework submissions by students
    Schema::create('homework_submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('homework_id')->constrained('homework')->cascadeOnDelete();
        $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        $table->string('attachment')->nullable();
        $table->text('remarks')->nullable();
        $table->enum('status', ['submitted','reviewed','late'])->default('submitted');
        $table->timestamp('submitted_at')->nullable();
        $table->timestamps();
        $table->unique(['homework_id', 'student_id']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework');
    }
};
