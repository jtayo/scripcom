<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('hotspot_id')->constrained('wifi_packages')->nullOnDelete();
            $table->unsignedInteger('max_uses')->nullable()->after('value');
            $table->unsignedInteger('used_count')->default(0)->after('max_uses');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('package_id');
            $table->dropColumn(['max_uses', 'used_count']);
        });
    }
};
