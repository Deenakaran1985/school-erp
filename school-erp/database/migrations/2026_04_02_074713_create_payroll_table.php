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
    Schema::create('payroll', function (Blueprint $table) {
        $table->id();
        $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
        $table->foreignId('academic_year_id')->constrained('academic_years');
        $table->string('month_year', 7);             // "2025-06"
        $table->integer('working_days')->default(26);
        $table->integer('present_days')->default(26);
        $table->integer('leave_days')->default(0);
        $table->decimal('basic_salary', 10, 2);
        $table->decimal('da_amount', 10, 2)->default(0);
        $table->decimal('hra_amount', 10, 2)->default(0);
        $table->decimal('other_allowance', 10, 2)->default(0);
        $table->decimal('gross_salary', 10, 2);
        $table->decimal('pf_deduction', 10, 2)->default(0);
        $table->decimal('esi_deduction', 10, 2)->default(0);
        $table->decimal('tds_deduction', 10, 2)->default(0);
        $table->decimal('loan_deduction', 10, 2)->default(0);
        $table->decimal('other_deduction', 10, 2)->default(0);
        $table->decimal('total_deduction', 10, 2)->default(0);
        $table->decimal('net_salary', 10, 2);
        $table->enum('status', ['draft','approved','paid'])->default('draft');
        $table->string('payment_mode', 20)->nullable();
        $table->date('paid_on')->nullable();
        $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        $table->text('remarks')->nullable();
        $table->timestamps();

        $table->unique(['staff_id', 'month_year']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll');
    }
};
