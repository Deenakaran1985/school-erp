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
    // Expense heads / categories first
    Schema::create('expense_heads', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100);           // Stationery, Maintenance, Salary, Fuel
        $table->string('code', 20)->unique();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('expense_head_id')->constrained('expense_heads');
        $table->foreignId('created_by')->constrained('users');
        $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        $table->string('title', 200);
        $table->text('description')->nullable();
        $table->decimal('amount', 10, 2);
        $table->string('vendor_name', 100)->nullable();
        $table->string('bill_no', 50)->nullable();
        $table->string('attachment')->nullable();        // bill image/pdf
        $table->date('expense_date');
        $table->enum('status', ['pending','approved','rejected'])->default('pending');
        $table->timestamps();
        $table->index('expense_date');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
