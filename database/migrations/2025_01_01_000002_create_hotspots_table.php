<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('router_id')->nullable()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('ssid')->nullable();
            $table->string('device_model')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('mac_address', 17)->nullable();
            $table->string('isp')->nullable();
            $table->integer('bandwidth_up')->nullable();
            $table->integer('bandwidth_down')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('ward')->nullable();
            $table->string('sub_county')->nullable();
            $table->string('status')->default('online');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_online_at')->nullable();
            $table->integer('max_clients')->default(50);
            $table->boolean('is_active')->default(true);
            $table->json('configuration')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotspots');
    }
};
