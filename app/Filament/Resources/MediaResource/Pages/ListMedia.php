<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use App\Models\MediaLibrary;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload_media')
                ->label('Upload Media Asset')
                ->icon('heroicon-m-arrow-up-tray')
                ->modalWidth('2xl')
                ->form([
                    FileUpload::make('file')
                        ->label('Select File')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'application/pdf'])
                        ->maxSize(10240)
                        ->disk('public')
                        ->directory('media-library/tmp')
                        ->required(),

                    TextInput::make('name')
                        ->label('Asset Title')
                        ->placeholder('Optional descriptive title'),

                    Select::make('collection_name')
                        ->label('Collection')
                        ->options([
                            'images'    => 'Images',
                            'documents' => 'Documents',
                            'default'   => 'Default',
                        ])
                        ->default('images')
                        ->required(),

                    TextInput::make('alt_text')
                        ->label('Alt Text (Accessibility & SEO)')
                        ->placeholder('Describe the image for screen readers'),

                    Textarea::make('caption')
                        ->label('Caption')
                        ->rows(2)
                        ->placeholder('Optional caption displayed beneath the image'),

                    TextInput::make('credits')
                        ->label('Credits / Attribution')
                        ->placeholder('e.g. Photo by Jane Doe / Unsplash'),
                ])
                ->action(function (array $data): void {
                    $tmpPath = $data['file']; // relative path on the 'public' disk

                    if (! $tmpPath || ! Storage::disk('public')->exists($tmpPath)) {
                        Notification::make()
                            ->title('Upload failed — file not found.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $library     = MediaLibrary::getInstance();
                    $absolutePath = Storage::disk('public')->path($tmpPath);
                    $originalName = basename($tmpPath);

                    $media = $library
                        ->addMedia($absolutePath)
                        ->usingName($data['name'] ?: pathinfo($originalName, PATHINFO_FILENAME))
                        ->usingFileName($originalName)
                        ->withCustomProperties([
                            'alt_text' => $data['alt_text'] ?? null,
                            'caption'  => $data['caption']  ?? null,
                            'credits'  => $data['credits']  ?? null,
                        ])
                        ->toMediaCollection($data['collection_name'] ?? 'images');

                    Notification::make()
                        ->title('Media uploaded successfully')
                        ->body($media->name . ' has been added to the library.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
