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
    Schema::create('fee_structures', function (Blueprint $table) {
        $table->id();
        $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
        $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
        $table->string('fee_head', 100);        // Tuition, Transport, Lab, Sports
        $table->decimal('amount', 10, 2);
        $table->enum('term', ['term1','term2','term3','annual','monthly'])->default('term1');
        $table->date('due_date')->nullable();
        $table->boolean('is_optional')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
