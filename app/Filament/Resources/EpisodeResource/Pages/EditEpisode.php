<?php

namespace App\Filament\Resources\EpisodeResource\Pages;

use App\Filament\Resources\EpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEpisode extends EditRecord
{
    protected static string $resource = EpisodeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Episódio atualizado com sucesso!';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
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