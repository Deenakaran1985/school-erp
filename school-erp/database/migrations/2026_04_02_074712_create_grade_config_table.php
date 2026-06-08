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
    Schema::create('grade_config', function (Blueprint $table) {
        $table->id();
        $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
        $table->decimal('min_percent', 5, 2);
        $table->decimal('max_percent', 5, 2);
        $table->string('grade', 5);           // A+, A, B+, B, C+, C, D, F
        $table->decimal('grade_point', 3, 2); // 10.0 to 0.0
        $table->string('description', 50)->nullable(); // Outstanding, Excellent...
        $table->timestamps();

        $table->index(['academic_year_id', 'min_percent']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_config');
    }
};
