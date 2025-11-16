<?php

namespace App\Filament\Resources\EpisodeResource\Pages;

use App\Filament\Resources\EpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEpisode extends ViewRecord
{
    protected static string $resource = EpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('test_stream')
                ->label('Testar Streaming')
                ->icon('heroicon-o-play')
                ->color('info')
                ->url(fn (): string => '/api/stream/' . $this->record->id)
                ->openUrlInNewTab(),
            Actions\Action::make('watch_video')
                ->label('Assistir Vídeo')
                ->icon('heroicon-o-video-camera')
                ->color('success')
                ->url(fn (): string => '/api/stream/' . $this->record->id)
                ->openUrlInNewTab(),
        ];
    }
}