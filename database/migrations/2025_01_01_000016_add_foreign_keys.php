<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
            $table->foreign('sponsor_id')->references('id')->on('sponsors')->nullOnDelete();
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('campaign_hotspot', function (Blueprint $table) {
            $table->foreign('hotspot_id')->references('id')->on('hotspots')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['sponsor_id']);
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('campaign_hotspot', function (Blueprint $table) {
            $table->dropForeign(['hotspot_id']);
        });
    }
};
