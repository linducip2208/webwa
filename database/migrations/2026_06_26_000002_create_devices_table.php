<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('session_name')->unique();
            $table->string('backend')->default('web'); // web | cloud
            $table->string('status')->default('disconnected'); // disconnected|connecting|qr|authenticated|ready|connected|auth_failure|error
            $table->string('phone')->nullable();
            $table->string('push_name')->nullable();
            $table->string('webhook_url')->nullable();
            $table->json('webhook_events')->nullable();
            $table->string('cloud_phone_number_id')->nullable();
            $table->text('cloud_access_token')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
