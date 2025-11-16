<?php

namespace App\Filament\Widgets;

use App\Models\Dorama;
use App\Models\Episode;
use App\Models\User;
use App\Models\WatchHistory;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OverviewStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Doramas', Dorama::count())
                ->description('Total no catálogo')
                ->color('primary'),

            Stat::make('Episódios', Episode::count())
                ->description('Total de episódios')
                ->color('info'),

            Stat::make('Usuários', User::count())
                ->description('Usuários cadastrados')
                ->color('success'),

            Stat::make('Visualizações', WatchHistory::count())
                ->description('Total de visualizações')
                ->color('warning'),
        ];
    }
}