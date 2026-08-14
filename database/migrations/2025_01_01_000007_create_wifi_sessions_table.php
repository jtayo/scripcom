<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wifi_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_id')->unique();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotspot_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sponsorship_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider_session_id')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('mac_address', 17)->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('auth_method')->default('otp');
            $table->boolean('video_completed')->default(false);
            $table->integer('video_watch_duration')->default(0);
            $table->integer('total_duration')->default(0);
            $table->unsignedBigInteger('bandwidth_used')->default(0);
            $table->unsignedBigInteger('bandwidth_up')->default(0);
            $table->unsignedBigInteger('bandwidth_down')->default(0);
            $table->timestamp('session_started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->string('status')->default('active');
            $table->string('end_reason')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'session_started_at']);
            $table->index(['hotspot_id', 'status']);
            $table->index(['status', 'last_heartbeat_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wifi_sessions');
    }
};
