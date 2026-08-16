<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenue_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('wifi_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotspot_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sponsorship_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('wifi_packages')->nullOnDelete();
            $table->string('source', 30);
            $table->string('description')->nullable();
            $table->decimal('gross_amount', 14, 2)->default(0);
            $table->decimal('payment_fee', 14, 2)->default(0);
            $table->decimal('net_amount', 14, 2)->default(0);
            $table->string('currency', 3)->default('KES');
            $table->date('revenue_date');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'revenue_date']);
            $table->index(['source', 'revenue_date']);
            $table->index(['hotspot_id', 'revenue_date']);
            $table->index(['campaign_id', 'revenue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_records');
    }
};
