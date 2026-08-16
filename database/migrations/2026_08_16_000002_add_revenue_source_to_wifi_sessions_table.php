<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wifi_sessions', function (Blueprint $table) {
            $table->string('revenue_source', 30)->nullable()->after('auth_method');
        });
    }

    public function down(): void
    {
        Schema::table('wifi_sessions', function (Blueprint $table) {
            $table->dropColumn('revenue_source');
        });
    }
};
