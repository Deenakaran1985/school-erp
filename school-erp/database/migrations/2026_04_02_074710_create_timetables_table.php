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
    Schema::create('timetables', function (Blueprint $table) {
        $table->id();
        $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
        $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
        $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
        $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
        $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
        $table->enum('day', ['Mon','Tue','Wed','Thu','Fri','Sat']);
        $table->integer('period_no');             // 1 to 8
        $table->time('start_time');
        $table->time('end_time');
        $table->boolean('is_active')->default(true);
        $table->timestamps();

        $table->unique(['section_id','day','period_no','academic_year_id'], 'timetable_unique');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
