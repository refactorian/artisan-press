<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            AuthorSeeder::class,
            TaxonomySeeder::class,
            SeriesSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            PageAndNavigationSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
