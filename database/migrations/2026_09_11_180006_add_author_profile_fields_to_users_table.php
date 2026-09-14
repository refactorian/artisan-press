<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('job_title')->nullable()->after('bio');
            $table->string('pronouns')->nullable()->after('job_title');
            $table->string('website_url')->nullable()->after('pronouns');
            $table->jsonb('social_links')->nullable()->after('website_url');
            $table->boolean('is_featured_author')->default(false)->after('social_links');

            $table->index('is_featured_author');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'job_title',
                'pronouns',
                'website_url',
                'social_links',
                'is_featured_author',
            ]);
        });
    }
};
