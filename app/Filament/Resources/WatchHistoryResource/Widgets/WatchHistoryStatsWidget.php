<?php

namespace App\Filament\Resources\WatchHistoryResource\Widgets;

use App\Models\WatchHistory;
use App\Models\User;
use App\Models\Episode;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WatchHistoryStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $totalWatchHistory = WatchHistory::count();
        $totalCompleted = WatchHistory::where('is_completed', true)->count();
        $totalUsers = User::count();
        $totalEpisodes = Episode::count();

        // Calculate completion rate
        $completionRate = $totalWatchHistory > 0
            ? round(($totalCompleted / $totalWatchHistory) * 100, 1)
            : 0;

        // Calculate today's watch time
        $todayWatchTime = WatchHistory::whereDate('watched_at', today())
            ->sum('progress_seconds');

        // Calculate average progress
        $avgProgress = WatchHistory::avg('progress_seconds') ?? 0;

        return [
            Stat::make('Total de Históricos', $totalWatchHistory)
                ->description('Registros de visualização')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),

            Stat::make('Episódios Concluídos', $totalCompleted)
                ->description($completionRate . '% de conclusão')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([2, 5, 3, 8, 6, 12, 9])
                ->color('success'),

            Stat::make('Usuários Ativos', $totalUsers)
                ->description('Usuários cadastrados')
                ->descriptionIcon('heroicon-m-users')
                ->chart([10, 15, 12, 18, 20, 25, 30])
                ->color('info'),

            Stat::make('Tempo Hoje', gmdate('H:i:s', $todayWatchTime))
                ->description('Tempo assistido hoje')
                ->descriptionIcon('heroicon-m-play-circle')
                ->chart([1200, 1800, 2400, 3600, 4200, 4800, 5400])
                ->color('warning'),

            Stat::make('Episódios Disponíveis', $totalEpisodes)
                ->description('No catálogo')
                ->descriptionIcon('heroicon-m-film')
                ->color('purple'),

            Stat::make('Progresso Médio', gmdate('H:i:s', (int)$avgProgress))
                ->description('Por episódio')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('indigo'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}