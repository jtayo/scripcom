<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sponsorship_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotspot_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('wifi_sessions')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('batch_id')->nullable();
            $table->string('type')->default('sessions');
            $table->integer('value')->default(1);
            $table->string('status')->default('unused');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('redeemed_phone', 30)->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();

            $table->index(['batch_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
