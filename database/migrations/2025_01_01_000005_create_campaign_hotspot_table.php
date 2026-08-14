<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_hotspot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('hotspot_id')->nullable();
            $table->unsignedBigInteger('router_id')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'router_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_hotspot');
    }
};
