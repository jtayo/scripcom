<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sponsorship_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone', 30)->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency')->default('KES');
            $table->string('status')->default('pending');
            $table->string('checkout_request_id')->nullable();
            $table->string('mpesa_receipt_number')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('result_code')->nullable();
            $table->text('result_description')->nullable();
            $table->json('callback_payload')->nullable();
            $table->timestamp('transacted_at')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['checkout_request_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
