<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert all json columns to jsonb.
     *
     * PostgreSQL's `json` type has no equality operator, which means:
     *  - SELECT DISTINCT queries on tables with json columns fail.
     *  - Filament's relationship Select fields trigger DISTINCT internally.
     *
     * `jsonb` supports equality (and is also faster for reads). This is a
     * safe, lossless cast — all existing data is preserved.
     */
    public function up(): void
    {
        $conversions = [
            'activity_log' => ['properties', 'attribute_changes'],
            'media' => ['custom_properties', 'generated_conversions', 'manipulations', 'responsive_images'],
            'pages' => ['content_blocks'],
            'post_revisions' => ['content_blocks'],
            'posts' => ['content_blocks'],
            'users' => ['social_links'],
        ];

        foreach ($conversions as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    DB::statement(
                        "ALTER TABLE \"{$table}\" ALTER COLUMN \"{$column}\" TYPE jsonb USING \"{$column}\"::jsonb"
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $conversions = [
            'activity_log' => ['properties', 'attribute_changes'],
            'media' => ['custom_properties', 'generated_conversions', 'manipulations', 'responsive_images'],
            'pages' => ['content_blocks'],
            'post_revisions' => ['content_blocks'],
            'posts' => ['content_blocks'],
            'users' => ['social_links'],
        ];

        foreach ($conversions as $table => $columns) {
            foreach ($columns as $column) {
                DB::statement(
                    "ALTER TABLE \"{$table}\" ALTER COLUMN \"{$column}\" TYPE json USING \"{$column}\"::text::json"
                );
            }
        }
    }
};
