<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ContentBlocksBuilder
{
    public static function make(string $name = 'content_blocks'): Builder
    {
        return Builder::make($name)
            ->label('Content Blocks & Components')
            ->collapsed()
            ->collapsible()
            ->cloneable()
            ->blocks([
                Block::make('callout')
                    ->label('Callout / Notice')
                    ->icon('heroicon-m-information-circle')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('type')
                                ->label('Callout Type')
                                ->options([
                                    'info' => 'Information (Blue)',
                                    'success' => 'Success (Green)',
                                    'warning' => 'Warning (Amber)',
                                    'danger' => 'Danger (Red)',
                                ])
                                ->default('info')
                                ->required(),
                            TextInput::make('title')
                                ->label('Title')
                                ->placeholder('Note / Tip / Warning'),
                        ]),
                        Textarea::make('content')
                            ->label('Message')
                            ->rows(3)
                            ->required(),
                    ]),

                Block::make('quote')
                    ->label('Quote / Testimonial')
                    ->icon('heroicon-m-chat-bubble-bottom-center-text')
                    ->schema([
                        Textarea::make('quote')
                            ->label('Quote Text')
                            ->rows(3)
                            ->required(),
                        Grid::make(2)->schema([
                            TextInput::make('author')
                                ->label('Author / Speaker')
                                ->required(),
                            TextInput::make('citation')
                                ->label('Source / Title / Company')
                                ->placeholder('e.g. CEO at TechCorp'),
                        ]),
                    ]),

                Block::make('code')
                    ->label('Code Snippet')
                    ->icon('heroicon-m-code-bracket')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('language')
                                ->label('Language')
                                ->options([
                                    'php' => 'PHP',
                                    'javascript' => 'JavaScript',
                                    'typescript' => 'TypeScript',
                                    'bash' => 'Bash / Shell',
                                    'html' => 'HTML',
                                    'css' => 'CSS',
                                    'python' => 'Python',
                                    'json' => 'JSON',
                                    'sql' => 'SQL',
                                    'yaml' => 'YAML / Markdown',
                                ])
                                ->default('php')
                                ->searchable()
                                ->required(),
                            TextInput::make('filename')
                                ->label('File Name / Path (Optional)')
                                ->placeholder('e.g. app/Models/Post.php'),
                        ]),
                        Textarea::make('code')
                            ->label('Code')
                            ->rows(8)
                            ->extraInputAttributes(['class' => 'font-mono text-sm'])
                            ->required(),
                    ]),

                Block::make('gallery')
                    ->label('Image Gallery / Visual')
                    ->icon('heroicon-m-photo')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Upload Images')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->directory('content-blocks')
                            ->maxFiles(6)
                            ->required(),
                        Grid::make(2)->schema([
                            Select::make('columns')
                                ->label('Columns Layout')
                                ->options([
                                    '1' => '1 Column (Full Width)',
                                    '2' => '2 Columns Grid',
                                    '3' => '3 Columns Grid',
                                ])
                                ->default('2'),
                            TextInput::make('caption')
                                ->label('Gallery Caption')
                                ->placeholder('Description or credit for the images'),
                        ]),
                    ]),

                Block::make('video')
                    ->label('Video Embed')
                    ->icon('heroicon-m-video-camera')
                    ->schema([
                        TextInput::make('url')
                            ->label('Video URL (YouTube, Vimeo, MP4)')
                            ->url()
                            ->required(),
                        Grid::make(2)->schema([
                            Select::make('aspect_ratio')
                                ->label('Aspect Ratio')
                                ->options([
                                    '16/9' => '16:9 (Standard Widescreen)',
                                    '4/3' => '4:3 (Classic)',
                                    '1/1' => '1:1 (Square)',
                                ])
                                ->default('16/9'),
                            TextInput::make('caption')
                                ->label('Video Caption / Title'),
                        ]),
                    ]),

                Block::make('key_takeaways')
                    ->label('Key Takeaways / Highlights')
                    ->icon('heroicon-m-check-badge')
                    ->schema([
                        TextInput::make('title')
                            ->label('Section Title')
                            ->default('Key Takeaways')
                            ->required(),
                        Repeater::make('items')
                            ->label('Points')
                            ->schema([
                                TextInput::make('point')
                                    ->label('Highlight / Takeaway')
                                    ->required(),
                            ])
                            ->collapsible()
                            ->defaultItems(3),
                    ]),

                Block::make('cta')
                    ->label('Call to Action Button')
                    ->icon('heroicon-m-cursor-arrow-rays')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('button_text')
                                ->label('Button Text')
                                ->required(),
                            TextInput::make('button_url')
                                ->label('Target URL')
                                ->url()
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('button_style')
                                ->label('Style')
                                ->options([
                                    'primary' => 'Primary (Filled)',
                                    'secondary' => 'Secondary (Subtle)',
                                    'outline' => 'Outline',
                                ])
                                ->default('primary'),
                            Select::make('target')
                                ->label('Open Target')
                                ->options([
                                    '_self' => 'Same Tab (_self)',
                                    '_blank' => 'New Tab (_blank)',
                                ])
                                ->default('_blank'),
                        ]),
                    ]),
            ]);
    }
}
