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
    Schema::create('student_promotions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        $table->foreignId('academic_year_id')->constrained('academic_years');
        $table->foreignId('from_class_id')->constrained('school_classes');
        $table->foreignId('to_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
        $table->foreignId('promoted_by')->constrained('users');
        $table->decimal('final_percentage', 5, 2)->nullable();
        $table->decimal('cgpa', 4, 2)->nullable();
        $table->string('final_grade', 5)->nullable();
        $table->enum('status', ['promoted','detained','transferred','passed_out']);
        $table->text('remarks')->nullable();
        $table->timestamp('promoted_at')->nullable();
        $table->timestamps();

        $table->unique(['student_id', 'academic_year_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
    }
};
