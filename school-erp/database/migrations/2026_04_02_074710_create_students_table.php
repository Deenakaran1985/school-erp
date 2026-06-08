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
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('academic_year_id')->constrained('academic_years');
        $table->foreignId('school_class_id')->constrained('school_classes');
        $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
        $table->foreignId('parent_user_id')->nullable()->constrained('users')->nullOnDelete();

        // Identity
        $table->string('admission_no', 30)->unique();
        $table->string('emis_number', 20)->unique()->nullable();  // from EMIS portal
        $table->string('name', 100);
        $table->string('father_name', 100);
        $table->string('mother_name', 100)->nullable();
        $table->date('date_of_birth');
        $table->enum('gender', ['M','F','O']);
        $table->string('blood_group', 5)->nullable();
        $table->string('photo')->nullable();

        // Community / Category (for Tamil Nadu govt schools)
        $table->string('community', 10)->nullable(); // BC, MBC, OC, SC, ST
        $table->string('caste', 50)->nullable();
        $table->string('religion', 30)->nullable();
        $table->string('mother_tongue', 30)->nullable();

        // Contact
        $table->string('parent_mobile', 15);
        $table->string('alt_mobile', 15)->nullable();
        $table->text('address')->nullable();
        $table->string('pincode', 10)->nullable();

        // ID Numbers
        $table->string('aadhar_number', 12)->nullable();
        $table->string('ration_card_no', 20)->nullable();

        // Academic
        $table->integer('roll_number')->nullable();
        $table->date('admission_date')->useCurrent();
        $table->enum('status', ['active','inactive','transferred','passed_out'])->default('active');

        // Transport
        $table->boolean('uses_transport')->default(false);

        $table->timestamps();
        $table->softDeletes();

        // Indexes
        $table->index('emis_number');
        $table->index(['school_class_id', 'section_id']);
        $table->index('status');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
