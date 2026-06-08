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
    Schema::create('fee_payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        $table->foreignId('fee_structure_id')->constrained('fee_structures');
        $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
        $table->string('receipt_no', 30)->unique();
        $table->decimal('amount_due', 10, 2);
        $table->decimal('amount_paid', 10, 2);
        $table->decimal('discount', 10, 2)->default(0);
        $table->decimal('fine', 10, 2)->default(0);
        $table->enum('payment_mode', ['cash','cheque','online','dd','bank_transfer'])->default('cash');
        $table->string('transaction_id', 100)->nullable();    // Razorpay order/payment ID
        $table->string('razorpay_order_id', 100)->nullable();
        $table->string('razorpay_signature', 255)->nullable();
        $table->enum('status', ['pending','paid','failed','refunded'])->default('pending');
        $table->date('payment_date')->nullable();
        $table->string('cheque_no', 30)->nullable();
        $table->string('bank_name', 100)->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();

        $table->index(['student_id', 'status']);
        $table->index('transaction_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
