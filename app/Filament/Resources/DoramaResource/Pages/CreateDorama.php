<?php

namespace App\Filament\Resources\DoramaResource\Pages;

use App\Filament\Resources\DoramaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDorama extends CreateRecord
{
    protected static string $resource = DoramaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Dorama criado com sucesso!';
    }
}