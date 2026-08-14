<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tolclin_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('event_type');
            $table->string('organization_ref')->nullable();
            $table->unsignedBigInteger('router_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tolclin_events');
    }
};
