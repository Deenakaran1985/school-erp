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
    Schema::create('sections', function (Blueprint $table) {
        $table->id();
        $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
        $table->string('name', 5);             // "A", "B", "C"
        $table->string('medium', 20)->default('Tamil'); // Tamil / English
        $table->integer('max_strength')->default(40);
        $table->foreignId('class_teacher_id')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();

        $table->unique(['school_class_id', 'name']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
