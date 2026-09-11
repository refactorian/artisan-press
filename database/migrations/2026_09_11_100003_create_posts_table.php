<?php

use App\Enums\PostStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Core content
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();

            // Status & publishing
            $table->string('status')->default(PostStatus::Draft->value);
            $table->timestamp('published_at')->nullable();

            // SEO fields
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('noindex')->default(false);
            $table->boolean('nofollow')->default(false);

            // Analytics
            $table->unsignedBigInteger('view_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index('slug');
            $table->index('status');
            $table->index('published_at');
            $table->index('user_id');
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
