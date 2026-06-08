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
    Schema::create('staff', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();

        // Identity
        $table->string('employee_id', 30)->unique();
        $table->string('name', 100);
        $table->date('date_of_birth')->nullable();
        $table->enum('gender', ['M','F','O']);
        $table->string('photo')->nullable();
        $table->string('aadhar_number', 12)->nullable();
        $table->string('pan_number', 10)->nullable();
        $table->string('qualification', 100)->nullable();

        // Job
        $table->string('designation', 100);
        $table->enum('staff_type', ['teaching','non_teaching','admin'])->default('teaching');
        $table->date('joining_date');
        $table->date('relieving_date')->nullable();

        // Salary
        $table->decimal('basic_salary', 10, 2)->default(0);
        $table->decimal('da_percent', 5, 2)->default(0);   // Dearness Allowance %
        $table->decimal('hra_percent', 5, 2)->default(0);  // House Rent Allowance %
        $table->decimal('other_allowance', 10, 2)->default(0);
        $table->decimal('pf_percent', 5, 2)->default(12);  // PF deduction %
        $table->string('bank_account', 20)->nullable();
        $table->string('bank_name', 100)->nullable();
        $table->string('bank_ifsc', 15)->nullable();

        $table->enum('status', ['active','inactive','relieved'])->default('active');
        $table->timestamps();
        $table->softDeletes();
        $table->index('employee_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
