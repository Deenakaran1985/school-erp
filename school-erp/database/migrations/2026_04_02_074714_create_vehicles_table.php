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
        Schema::create('vehicles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('route_id')->nullable()->constrained('transport_routes')->nullOnDelete();
        $table->string('vehicle_number', 20)->unique();  // TN33 AB 1234
        $table->string('vehicle_type', 30)->default('bus');
        $table->string('make_model', 100)->nullable();
        $table->integer('capacity')->default(40);
        $table->foreignId('driver_id')->nullable()->constrained('staff')->nullOnDelete();
        $table->date('insurance_expiry')->nullable();
        $table->date('fitness_expiry')->nullable();
        $table->date('permit_expiry')->nullable();
        $table->date('last_service_date')->nullable();
        $table->enum('status', ['active','maintenance','inactive'])->default('active');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
