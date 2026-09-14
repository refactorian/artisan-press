<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('series_id')->nullable()->after('user_id')->constrained('series')->nullOnDelete();
            $table->integer('series_order')->nullable()->after('series_id');
            $table->boolean('is_featured')->default(false)->after('status');
            $table->boolean('is_hero')->default(false)->after('is_featured');
            $table->integer('featured_order')->nullable()->after('is_hero');
            $table->integer('sort_order')->default(0)->after('featured_order');
            $table->jsonb('content_blocks')->nullable()->after('content');

            $table->index('is_featured');
            $table->index('is_hero');
            $table->index('sort_order');
            $table->index(['series_id', 'series_order']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropColumn([
                'series_id',
                'series_order',
                'is_featured',
                'is_hero',
                'featured_order',
                'sort_order',
                'content_blocks',
            ]);
        });
    }
};
