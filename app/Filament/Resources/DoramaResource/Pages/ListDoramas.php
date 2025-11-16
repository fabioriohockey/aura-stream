<?php

namespace App\Filament\Resources\DoramaResource\Pages;

use App\Filament\Resources\DoramaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDoramas extends ListRecords
{
    protected static string $resource = DoramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Novo Dorama'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widget removido temporariamente
        ];
    }
}