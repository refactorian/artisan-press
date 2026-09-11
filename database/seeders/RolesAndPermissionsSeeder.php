<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Posts
            'view posts',
            'create posts',
            'edit posts',
            'edit own posts',
            'delete posts',
            'delete own posts',
            'publish posts',
            'restore posts',
            'force delete posts',

            // Categories
            'view categories',
            'manage categories',

            // Tags
            'view tags',
            'manage tags',

            // Users
            'view users',
            'manage users',

            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Roles ──────────────────────────────────────────────────────────────

        // Super Admin — all permissions (gate bypassed via HasRoles::super_admin)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin — all blog management permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'view posts', 'create posts', 'edit posts', 'delete posts', 'publish posts', 'restore posts',
            'view categories', 'manage categories',
            'view tags', 'manage tags',
            'view users', 'manage users',
        ]);

        // Editor — can manage all posts, categories, tags, but not users
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->givePermissionTo([
            'view posts', 'create posts', 'edit posts', 'delete posts', 'publish posts',
            'view categories', 'manage categories',
            'view tags', 'manage tags',
        ]);

        // Author — can create posts and edit own posts only
        $author = Role::firstOrCreate(['name' => 'author']);
        $author->givePermissionTo([
            'view posts', 'create posts', 'edit own posts', 'delete own posts',
            'view categories',
            'view tags',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
