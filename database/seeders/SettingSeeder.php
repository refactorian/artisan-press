<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed global application and SEO settings.
     */
    public function run(): void
    {
        Setting::set('site_name', 'Laravel Modern Blog', 'general');
        Setting::set('site_tagline', 'Insights on Laravel, Livewire, and modern web architecture.', 'general');
        Setting::set('contact_email', 'editor@blog.test', 'general');
        Setting::set('default_seo_title', 'Laravel Modern Blog', 'seo');
        Setting::set('default_seo_desc', 'In-depth engineering articles on Laravel, Filament, Livewire, and modern cloud architectures.', 'seo');
        Setting::set('twitter_handle', '@laravelphp', 'seo');
        Setting::set('enable_comments', true, 'comments', 'boolean');
        Setting::set('auto_approve_comments', false, 'comments', 'boolean');
        Setting::set('search_driver', 'database', 'search');
    }
}
