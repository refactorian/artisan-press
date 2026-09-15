<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@blog.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $admin->assignRole('super_admin');

        // Create a sample editor user
        $editor = User::firstOrCreate(
            ['email' => 'editor@blog.test'],
            [
                'name' => 'Blog Editor',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $editor->assignRole('editor');

        // Create a sample author user
        $author = User::firstOrCreate(
            ['email' => 'author@blog.test'],
            [
                'name' => 'Blog Author',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $author->assignRole('author');

        $this->command->info('Admin users seeded successfully.');
        $this->command->table(
            ['Email', 'Password', 'Role'],
            [
                ['admin@blog.test',  'password', 'super_admin'],
                ['editor@blog.test', 'password', 'editor'],
                ['author@blog.test', 'password', 'author'],
            ]
        );
    }
}
