<?php

namespace App\Filament\Resources\WatchHistoryResource\Pages;

use App\Filament\Resources\WatchHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWatchHistory extends ViewRecord
{
    protected static string $resource = WatchHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('watch_episode')
                ->label('Assistir Episódio')
                ->icon('heroicon-o-play')
                ->color('success')
                ->url(fn (): string => '/api/stream/' . $this->record->episode_id)
                ->openUrlInNewTab(),
        ];
    }
}