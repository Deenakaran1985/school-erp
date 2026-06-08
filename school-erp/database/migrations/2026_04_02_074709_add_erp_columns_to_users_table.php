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
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone', 15)->nullable()->after('email');
        $table->string('avatar')->nullable()->after('phone');
        $table->string('fcm_token')->nullable()->after('avatar');  // Flutter FCM
        $table->enum('status', ['active','inactive','suspended'])->default('active')->after('fcm_token');
        $table->string('user_type', 20)->nullable()->after('status'); // admin/staff/student/parent
        $table->timestamp('last_login_at')->nullable()->after('user_type');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['phone','avatar','fcm_token','status','user_type','last_login_at']);
    });
}
};
