<?php

namespace App\Services;

use App\Models\Post;

class SocialShareService
{
    /**
     * Generate social sharing URLs for a post.
     *
     * @return array<string, string>
     */
    public function generateShareLinks(Post $post): array
    {
        $url = urlencode(url("/posts/{$post->slug}"));
        $title = urlencode($post->title);
        $summary = urlencode($post->excerpt ?? $post->title);

        return [
            'twitter' => "https://twitter.com/intent/tweet?url={$url}&text={$title}",
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$url}",
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$url}",
            'reddit' => "https://reddit.com/submit?url={$url}&title={$title}",
            'whatsapp' => "https://api.whatsapp.com/send?text={$title}%20{$url}",
            'telegram' => "https://t.me/share/url?url={$url}&text={$title}",
            'email' => "mailto:?subject={$title}&body={$summary}%0A%0A{$url}",
        ];
    }
}
