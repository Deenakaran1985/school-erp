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
    Schema::create('school_classes', function (Blueprint $table) {
        $table->id();
        $table->string('name', 20);          // "I", "II", ... "XII"
        $table->string('display_name', 50)->nullable(); // "Class 6"
        $table->enum('level', ['primary','middle','secondary','higher_secondary']);
        $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
        $table->integer('sort_order')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
