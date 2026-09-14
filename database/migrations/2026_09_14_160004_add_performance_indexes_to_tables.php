<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->index('view_count');
            $table->index('reading_time');
        });

        Schema::table('pages', function (Blueprint $table): void {
            $table->index(['status', 'published_at']);
        });

        Schema::table('subscribers', function (Blueprint $table): void {
            $table->index(['status', 'subscribed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropIndex(['view_count']);
            $table->dropIndex(['reading_time']);
        });

        Schema::table('pages', function (Blueprint $table): void {
            $table->dropIndex(['status', 'published_at']);
        });

        Schema::table('subscribers', function (Blueprint $table): void {
            $table->dropIndex(['status', 'subscribed_at']);
        });
    }
};
