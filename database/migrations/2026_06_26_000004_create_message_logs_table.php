<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('api_key_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction')->default('outbound'); // outbound | inbound
            $table->string('backend')->default('web'); // web | cloud
            $table->string('to_number')->nullable();
            $table->string('from_number')->nullable();
            $table->string('type')->default('text'); // text|image|video|audio|document|template|location
            $table->text('body')->nullable();
            $table->string('media_url')->nullable();
            $table->string('status')->default('queued'); // queued|sent|delivered|read|failed
            $table->string('wa_message_id')->nullable();
            $table->text('error')->nullable();
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->string('source')->default('api'); // api | dashboard | bulk
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['device_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
