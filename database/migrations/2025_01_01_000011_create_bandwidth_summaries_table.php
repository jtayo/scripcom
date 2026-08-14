<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bandwidth_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotspot_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->unsignedBigInteger('total_bandwidth')->default(0);
            $table->unsignedBigInteger('upload_bandwidth')->default(0);
            $table->unsignedBigInteger('download_bandwidth')->default(0);
            $table->unsignedInteger('session_count')->default(0);
            $table->timestamps();

            $table->unique(['organization_id', 'hotspot_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bandwidth_summaries');
    }
};
