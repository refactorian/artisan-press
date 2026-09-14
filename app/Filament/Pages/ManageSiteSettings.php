<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings & Audit';

    protected static ?string $navigationLabel = 'Site & Search Settings';

    protected static ?string $title = 'Site & Search Configuration';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            // General
            'site_name' => Setting::get('site_name', 'Laravel Modern Blog'),
            'site_tagline' => Setting::get('site_tagline', 'Insights, engineering, and architecture stories.'),
            'contact_email' => Setting::get('contact_email', 'hello@example.com'),
            'posts_per_page' => Setting::get('posts_per_page', 10),

            // SEO & Social
            'default_seo_title' => Setting::get('default_seo_title', 'Laravel Modern Blog - Latest Articles'),
            'default_seo_desc' => Setting::get('default_seo_desc', 'Explore high-quality articles on web development, Laravel, and cloud architectures.'),
            'twitter_handle' => Setting::get('twitter_handle', '@laravel'),
            'facebook_url' => Setting::get('facebook_url', ''),
            'github_url' => Setting::get('github_url', 'https://github.com'),

            // Header / Footer
            'footer_copyright' => Setting::get('footer_copyright', '© '.date('Y').' Laravel Blog. All rights reserved.'),
            'custom_head_scripts' => Setting::get('custom_head_scripts', ''),

            // Comments & Moderation
            'enable_comments' => Setting::get('enable_comments', true),
            'auto_approve_comments' => Setting::get('auto_approve_comments', false),
            'spam_keywords' => Setting::get('spam_keywords', 'crypto, casino, loans, viagra, buy-now'),

            // Search Configuration
            'search_driver' => Setting::get('search_driver', 'database'),
            'search_min_chars' => Setting::get('search_min_chars', 3),
            'search_highlight' => Setting::get('search_highlight', true),

            // Webhooks & Integrations
            'webhook_url' => Setting::get('webhook_url', ''),
            'webhook_secret' => Setting::get('webhook_secret', ''),
            'newsletter_provider' => Setting::get('newsletter_provider', 'database'),
            'newsletter_api_key' => Setting::get('newsletter_api_key', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('site_name')
                                        ->label('Site Name')
                                        ->required(),

                                    TextInput::make('site_tagline')
                                        ->label('Site Tagline / Slogan'),

                                    TextInput::make('contact_email')
                                        ->label('Public Contact Email')
                                        ->email()
                                        ->required(),

                                    TextInput::make('posts_per_page')
                                        ->label('Posts Per Page')
                                        ->numeric()
                                        ->default(10)
                                        ->required(),
                                ]),
                            ]),

                        Tab::make('SEO & Social')
                            ->icon('heroicon-o-share')
                            ->schema([
                                TextInput::make('default_seo_title')
                                    ->label('Default SEO Meta Title')
                                    ->maxLength(70),

                                Textarea::make('default_seo_desc')
                                    ->label('Default SEO Description')
                                    ->rows(3)
                                    ->maxLength(160),

                                Grid::make(3)->schema([
                                    TextInput::make('twitter_handle')
                                        ->label('Twitter / X Handle')
                                        ->placeholder('@handle'),

                                    TextInput::make('facebook_url')
                                        ->label('Facebook URL')
                                        ->url(),

                                    TextInput::make('github_url')
                                        ->label('GitHub URL')
                                        ->url(),
                                ]),
                            ]),

                        Tab::make('Header & Footer')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                TextInput::make('footer_copyright')
                                    ->label('Footer Copyright Notice')
                                    ->required(),

                                Textarea::make('custom_head_scripts')
                                    ->label('Header Tracking Scripts (<head>)')
                                    ->placeholder('<!-- Google Analytics / Plausible tracking snippet -->')
                                    ->rows(5)
                                    ->extraInputAttributes(['class' => 'font-mono text-sm']),
                            ]),

                        Tab::make('Comments & Moderation')
                            ->icon('heroicon-o-chat-bubble-bottom-center-text')
                            ->schema([
                                Grid::make(2)->schema([
                                    Toggle::make('enable_comments')
                                        ->label('Enable Comments System')
                                        ->helperText('Allow readers to submit comments on published posts.'),

                                    Toggle::make('auto_approve_comments')
                                        ->label('Auto-Approve Comments')
                                        ->helperText('When enabled, new comments bypass moderation immediately.'),
                                ]),

                                Textarea::make('spam_keywords')
                                    ->label('Spam Filter Blocklist')
                                    ->helperText('Comma-separated list of keywords that trigger automatic spam marking.')
                                    ->rows(3),
                            ]),

                        Tab::make('Search Engine & Index')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('search_driver')
                                        ->label('Search Driver')
                                        ->options([
                                            'database' => 'Database Full-Text (PostgreSQL)',
                                            'meilisearch' => 'Meilisearch Engine',
                                        ])
                                        ->default('database')
                                        ->required(),

                                    TextInput::make('search_min_chars')
                                        ->label('Minimum Query Characters')
                                        ->numeric()
                                        ->default(3)
                                        ->required(),
                                ]),

                                Toggle::make('search_highlight')
                                    ->label('Highlight Search Terms in Results')
                                    ->default(true),
                            ]),

                        Tab::make('Webhooks & Integrations')
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                TextInput::make('webhook_url')
                                    ->label('Outbound Webhook Endpoint')
                                    ->placeholder('https://api.example.com/webhooks/blog')
                                    ->url()
                                    ->helperText('Receives JSON payload on post.published, post.updated, and post.deleted events.'),

                                TextInput::make('webhook_secret')
                                    ->label('Webhook Secret (HMAC-SHA256)')
                                    ->password()
                                    ->revealable()
                                    ->helperText('Used to sign the X-Blog-Signature header on outgoing requests.'),

                                Grid::make(2)->schema([
                                    Select::make('newsletter_provider')
                                        ->label('Newsletter Service Provider')
                                        ->options([
                                            'database' => 'Internal Subscribers Database',
                                            'mailchimp' => 'Mailchimp',
                                            'convertkit' => 'Kit (ConvertKit)',
                                            'beehiiv' => 'Beehiiv',
                                        ])
                                        ->default('database'),

                                    TextInput::make('newsletter_api_key')
                                        ->label('Provider API Key / Token')
                                        ->password()
                                        ->revealable()
                                        ->helperText('External newsletter service credential.'),
                                ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            $group = match (true) {
                str_contains($key, 'seo') || str_contains($key, 'twitter') || str_contains($key, 'facebook') || str_contains($key, 'github') => 'seo',
                str_contains($key, 'footer') || str_contains($key, 'scripts') => 'header_footer',
                str_contains($key, 'comment') || str_contains($key, 'spam') => 'comments',
                str_contains($key, 'search') => 'search',
                str_contains($key, 'webhook') || str_contains($key, 'newsletter') => 'integrations',
                default => 'general',
            };

            $type = is_bool($value) ? 'boolean' : (is_numeric($value) ? 'integer' : 'string');
            Setting::set($key, $value, $group, $type);
        }

        Notification::make()
            ->title('Settings Saved Successfully')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save All Settings')
                ->submit('save')
                ->color('primary'),
        ];
    }
}
