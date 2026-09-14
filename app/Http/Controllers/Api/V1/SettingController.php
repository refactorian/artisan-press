<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /**
     * Display public site configuration and branding settings.
     */
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'site_name' => Setting::get('site_name', config('app.name', 'Laravel Modern Blog')),
            'site_tagline' => Setting::get('site_tagline', 'Insights, engineering, and architecture stories.'),
            'contact_email' => Setting::get('contact_email', 'hello@example.com'),
            'posts_per_page' => (int) Setting::get('posts_per_page', 10),
            'default_seo_title' => Setting::get('default_seo_title', 'Laravel Modern Blog'),
            'default_seo_desc' => Setting::get('default_seo_desc', 'Articles on web development.'),
            'social_links' => [
                'twitter' => Setting::get('twitter_handle'),
                'facebook' => Setting::get('facebook_url'),
                'github' => Setting::get('github_url'),
            ],
            'footer_copyright' => Setting::get('footer_copyright', '© '.date('Y').' Laravel Blog. All rights reserved.'),
            'enable_comments' => (bool) Setting::get('enable_comments', true),
        ]);
    }
}
