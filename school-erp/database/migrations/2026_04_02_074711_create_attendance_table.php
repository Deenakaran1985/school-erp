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
    Schema::create('attendance', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        $table->foreignId('school_class_id')->constrained('school_classes');
        $table->foreignId('section_id')->constrained('sections');
        $table->foreignId('marked_by')->constrained('users');   // teacher
        $table->date('attendance_date');
        $table->enum('status', ['present','absent','late','half_day','holiday'])->default('present');
        $table->string('remarks', 100)->nullable();
        $table->timestamps();

        $table->unique(['student_id', 'attendance_date'], 'attendance_unique');
        $table->index('attendance_date');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
