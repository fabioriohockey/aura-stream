<?php

namespace App\Filament\Resources\DoramaResource\Pages;

use App\Filament\Resources\DoramaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDorama extends ViewRecord
{
    protected static string $resource = DoramaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('view_site')
                ->label('Ver no Site')
                ->icon('heroicon-o-eye')
                ->color('success')
                ->url(fn (): string => '/doramas/' . $this->record->slug)
                ->openUrlInNewTab(),
            Actions\Action::make('manage_episodes')
                ->label('Gerenciar Episódios')
                ->icon('heroicon-o-tv')
                ->color('info')
                ->url(fn (): string => route('filament.admin.resources.episodes.index', ['dorama_id' => $this->record->id])),
        ];
    }
}