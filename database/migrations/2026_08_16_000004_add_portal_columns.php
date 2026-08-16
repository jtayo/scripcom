<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wifi_sessions', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('session_started_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('sponsorship_id')->constrained('wifi_packages')->nullOnDelete();
            $table->foreignId('hotspot_id')->nullable()->after('package_id')->constrained('hotspots')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wifi_sessions', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('package_id');
            $table->dropConstrainedForeignId('hotspot_id');
        });
    }
};
