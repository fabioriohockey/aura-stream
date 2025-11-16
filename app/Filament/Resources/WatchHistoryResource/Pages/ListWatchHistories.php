<?php

namespace App\Filament\Resources\WatchHistoryResource\Pages;

use App\Filament\Resources\WatchHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWatchHistories extends ListRecords
{
    protected static string $resource = WatchHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Novo Histórico'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WatchHistoryResource\Widgets\WatchHistoryStatsWidget::class,
        ];
    }
}