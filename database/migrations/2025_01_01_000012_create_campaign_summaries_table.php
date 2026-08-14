<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('total_plays')->default(0);
            $table->unsignedInteger('completions')->default(0);
            $table->unsignedInteger('avg_watch_duration')->default(0);
            $table->timestamps();

            $table->unique(['campaign_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_summaries');
    }
};
