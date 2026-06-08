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
        Schema::create('student_transport', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        $table->foreignId('route_id')->constrained('transport_routes');
        $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
        $table->string('pickup_stop', 100)->nullable();
        $table->foreignId('academic_year_id')->constrained('academic_years');
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->unique(['student_id', 'academic_year_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_transport');
    }
};
