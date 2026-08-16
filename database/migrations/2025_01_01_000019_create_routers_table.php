<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotspot_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedInteger('port')->default(8728);
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->string('status')->default('online');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_online_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
        });

        Schema::create('router_health_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('online');
            $table->decimal('cpu_usage', 5, 2)->nullable();
            $table->decimal('memory_usage', 5, 2)->nullable();
            $table->unsignedBigInteger('uptime_seconds')->nullable();
            $table->unsignedBigInteger('rx_bytes')->nullable();
            $table->unsignedBigInteger('tx_bytes')->nullable();
            $table->decimal('latency_ms', 10, 2)->nullable();
            $table->unsignedInteger('active_users')->nullable();
            $table->timestamp('recorded_at')->useCurrent();

            $table->index(['router_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('router_health_logs');
        Schema::dropIfExists('routers');
    }
};
