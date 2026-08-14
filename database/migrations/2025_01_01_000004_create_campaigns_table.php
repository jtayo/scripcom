<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreignId('sponsor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('commercial');
            $table->string('content_type')->default('image');
            $table->string('content_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('redirect_url')->nullable();
            $table->integer('duration_seconds')->default(15);
            $table->boolean('skip_allowed')->default(false);
            $table->boolean('is_mandatory')->default(false);
            $table->integer('priority')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('max_plays')->nullable();
            $table->integer('current_plays')->default(0);
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(true);
            $table->json('targeting_rules')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['sponsor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
