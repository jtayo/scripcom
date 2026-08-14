<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_aggregates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->unsignedInteger('total_sessions')->default(0);
            $table->unsignedInteger('active_sessions')->default(0);
            $table->unsignedInteger('completed_sessions')->default(0);
            $table->unsignedBigInteger('bandwidth_used')->default(0);
            $table->unsignedBigInteger('total_duration')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_aggregates');
    }
};
