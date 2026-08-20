<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_daily_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('device_token', 64)->nullable();
            $table->string('fingerprint_hash', 64)->nullable();
            $table->foreignId('wifi_session_id')->nullable()->constrained('wifi_sessions')->nullOnDelete();
            $table->date('viewed_date');
            $table->timestamps();

            $table->unique(['campaign_id', 'phone', 'viewed_date'], 'cdv_unique_phone');
            $table->index(['campaign_id', 'device_token', 'viewed_date'], 'cdv_idx_device');
            $table->index(['campaign_id', 'fingerprint_hash', 'viewed_date'], 'cdv_idx_fp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_daily_views');
    }
};
