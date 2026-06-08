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
    Schema::create('subjects', function (Blueprint $table) {
        $table->id();
        $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
        $table->string('name', 100);
        $table->string('code', 20);            // MATH, LANG1, SCI
        $table->enum('type', ['theory','practical','language','activity'])->default('theory');
        $table->boolean('is_core')->default(false);   // Tamil, Maths → true (no grace)
        $table->integer('max_marks')->default(100);
        $table->integer('pass_marks')->default(35);
        $table->integer('sort_order')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();

        $table->unique(['school_class_id', 'code']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
