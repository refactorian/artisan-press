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
        Schema::table('categories', function (Blueprint $table): void {
            $table->string('seo_title')->nullable()->after('description');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('canonical_url')->nullable()->after('seo_description');
            $table->boolean('noindex')->default(false)->after('canonical_url');
            $table->boolean('nofollow')->default(false)->after('noindex');
        });

        Schema::table('tags', function (Blueprint $table): void {
            $table->string('seo_title')->nullable()->after('name');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('canonical_url')->nullable()->after('seo_description');
            $table->boolean('noindex')->default(false)->after('canonical_url');
            $table->boolean('nofollow')->default(false)->after('noindex');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('seo_title')->nullable()->after('social_links');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('canonical_url')->nullable()->after('seo_description');
            $table->boolean('noindex')->default(false)->after('canonical_url');
            $table->boolean('nofollow')->default(false)->after('noindex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn(['seo_title', 'seo_description', 'canonical_url', 'noindex', 'nofollow']);
        });

        Schema::table('tags', function (Blueprint $table): void {
            $table->dropColumn(['seo_title', 'seo_description', 'canonical_url', 'noindex', 'nofollow']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['seo_title', 'seo_description', 'canonical_url', 'noindex', 'nofollow']);
        });
    }
};
