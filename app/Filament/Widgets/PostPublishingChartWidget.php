<?php

namespace App\Filament\Widgets;

use App\Enums\PostStatus;
use App\Models\Post;
use Filament\Widgets\ChartWidget;

class PostPublishingChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Publishing Status Breakdown';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected function getData(): array
    {
        $published = Post::where('status', PostStatus::Published)->count();
        $draft = Post::where('status', PostStatus::Draft)->count();
        $scheduled = Post::where('status', PostStatus::Scheduled)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Articles',
                    'data' => [$published, $draft, $scheduled],
                    'backgroundColor' => [
                        '#6366f1', // Indigo (Published)
                        '#71717a', // Zinc (Draft)
                        '#f59e0b', // Amber (Scheduled)
                    ],
                    'borderWidth' => 2,
                    'borderColor' => 'transparent',
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Published', 'Drafts', 'Scheduled'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 16,
                    ],
                ],
            ],
            'cutout' => '72%',
        ];
    }
}
