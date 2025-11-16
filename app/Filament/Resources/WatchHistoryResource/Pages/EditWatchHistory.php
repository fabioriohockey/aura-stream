<?php

namespace App\Filament\Resources\WatchHistoryResource\Pages;

use App\Filament\Resources\WatchHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWatchHistory extends EditRecord
{
    protected static string $resource = WatchHistoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Histórico atualizado com sucesso!';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('watch_episode')
                ->label('Assistir Episódio')
                ->icon('heroicon-o-play')
                ->color('success')
                ->url(fn (): string => '/api/stream/' . $this->record->episode_id)
                ->openUrlInNewTab(),
        ];
    }
}