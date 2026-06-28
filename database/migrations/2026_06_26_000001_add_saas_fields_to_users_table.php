<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email');
            $table->string('plan')->default('free')->after('role');
            $table->unsignedInteger('device_limit')->default(1)->after('plan');
            $table->unsignedInteger('monthly_quota')->default(1000)->after('device_limit');
            $table->boolean('is_active')->default(true)->after('monthly_quota');
            $table->string('company')->nullable()->after('is_active');
            $table->string('phone')->nullable()->after('company');
            $table->timestamp('last_login_at')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'plan', 'device_limit', 'monthly_quota',
                'is_active', 'company', 'phone', 'last_login_at',
            ]);
        });
    }
};
