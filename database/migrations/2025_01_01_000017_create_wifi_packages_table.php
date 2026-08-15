<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wifi_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('duration_minutes');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('access_type')->default('free');
            $table->integer('bandwidth_down_kbps')->nullable();
            $table->integer('bandwidth_up_kbps')->nullable();
            $table->bigInteger('data_limit_mb')->nullable();
            $table->integer('simultaneous_devices')->default(1);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'access_type']);
            $table->index(['organization_id', 'is_active']);
        });

        Schema::table('wifi_sessions', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('campaign_id')->constrained('wifi_packages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wifi_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('package_id');
        });

        Schema::dropIfExists('wifi_packages');
    }
};
