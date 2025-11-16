<?php

namespace App\Filament\Resources\DoramaResource\Pages;

use App\Filament\Resources\DoramaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDorama extends EditRecord
{
    protected static string $resource = DoramaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Dorama atualizado com sucesso!';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('view_site')
                ->label('Ver no Site')
                ->icon('heroicon-o-eye')
                ->color('success')
                ->url(fn (): string => '/doramas/' . $this->record->slug)
                ->openUrlInNewTab(),
        ];
    }
}