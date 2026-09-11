<?php

namespace App\Filament\Widgets;

use App\Enums\PostStatus;
use App\Models\Post;
use Filament\Widgets\ChartWidget;

class PostPublishingChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Post Status Distribution';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $published = Post::where('status', PostStatus::Published)->count();
        $draft = Post::where('status', PostStatus::Draft)->count();
        $scheduled = Post::where('status', PostStatus::Scheduled)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Posts',
                    'data' => [$published, $draft, $scheduled],
                    'backgroundColor' => ['#10b981', '#6b7280', '#f59e0b'],
                ],
            ],
            'labels' => ['Published', 'Drafts', 'Scheduled'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
