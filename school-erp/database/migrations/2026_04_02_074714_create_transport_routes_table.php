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
       Schema::create('transport_routes', function (Blueprint $table) {
        $table->id();
        $table->string('route_name', 100);
        $table->string('route_number', 20)->unique();
        $table->text('stops')->nullable();     // JSON: ["Stop A","Stop B"]
        $table->time('pickup_start_time')->nullable();
        $table->time('drop_end_time')->nullable();
        $table->decimal('monthly_fee', 8, 2)->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_routes');
    }
};
