<?php

namespace App\Filament\Resources\WatchHistoryResource\Pages;

use App\Filament\Resources\WatchHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWatchHistory extends CreateRecord
{
    protected static string $resource = WatchHistoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Histórico criado com sucesso!';
    }
}