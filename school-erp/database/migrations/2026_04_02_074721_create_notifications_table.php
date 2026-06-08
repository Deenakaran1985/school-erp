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
    Schema::create('school_notifications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sent_by')->constrained('users');
        $table->string('title', 200);
        $table->text('body');
        $table->string('type', 30)->default('general'); // exam_result, fee_due, general
        $table->string('target_role', 30)->nullable();   // all, parent, teacher
        $table->foreignId('target_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
        $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
        $table->json('data')->nullable();              // extra payload for Flutter
        $table->integer('sent_count')->default(0);
        $table->timestamp('sent_at')->nullable();
        $table->timestamps();
        $table->index(['type', 'sent_at']);
    });

    // Track read status per user
    Schema::create('notification_reads', function (Blueprint $table) {
        $table->id();
        $table->foreignId('notification_id')->constrained('school_notifications')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->timestamp('read_at')->nullable();
        $table->timestamps();
        $table->unique(['notification_id', 'user_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
