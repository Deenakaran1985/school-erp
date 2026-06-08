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
    Schema::create('exam_types', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100);           // "Unit Test", "Half Yearly"
        $table->string('code', 10)->unique();  // UT, QE, HY, AE, SUPP
        $table->decimal('weightage_percent', 5, 2)->default(0); // 10, 25, 25, 40
        $table->integer('max_marks')->default(100);
        $table->integer('pass_marks')->default(35);
        $table->boolean('counts_for_promotion')->default(true);
        $table->integer('sort_order')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_types');
    }
};
