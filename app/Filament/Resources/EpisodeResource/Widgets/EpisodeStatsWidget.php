<?php

namespace App\Filament\Resources\EpisodeResource\Widgets;

use App\Models\Episode;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EpisodeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalEpisodes = Episode::count();
        $activeEpisodes = Episode::where('is_active', true)->count();
        $premiumEpisodes = Episode::where('is_premium_only', true)->count();
        $freeEpisodes = Episode::where('is_premium_only', false)->count();

        // Calcular tamanho total dos vídeos
        $totalSize480p = Episode::sum('file_size_480p_mb');
        $totalSize720p = Episode::sum('file_size_720p_mb');

        // Calcular visualizações totais
        $totalViews = Episode::sum('views_count');

        return [
            Stat::make('Total de Episódios', $totalEpisodes)
                ->description('Todos os episódios cadastrados')
                ->descriptionIcon('heroicon-m-film')
                ->chart([7, 12, 10, 14, 15, 18, 20])
                ->color('primary'),

            Stat::make('Episódios Ativos', $activeEpisodes)
                ->description($totalEpisodes > 0 ? round(($activeEpisodes / $totalEpisodes) * 100, 1) . '% do total' : '0%')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([5, 8, 6, 10, 12, 14, 16])
                ->color('success'),

            Stat::make('Episódios Premium', $premiumEpisodes)
                ->description('Apenas para assinantes')
                ->descriptionIcon('heroicon-m-crown')
                ->chart([2, 3, 5, 7, 8, 10, 12])
                ->color('warning'),

            Stat::make('Visualizações', $totalViews)
                ->description('Total de visualizações')
                ->descriptionIcon('heroicon-m-eye')
                ->chart([100, 250, 500, 800, 1200, 1800, 2500])
                ->color('info'),

            Stat::make('Tamanho 480p', number_format($totalSize480p, 1, ',', '.') . ' GB')
                ->description('Espaço usado em 480p')
                ->descriptionIcon('heroicon-m-server')
                ->color('blue'),

            Stat::make('Tamanho 720p', number_format($totalSize720p, 1, ',', '.') . ' GB')
                ->description('Espaço usado em 720p')
                ->descriptionIcon('heroicon-m-server-stack')
                ->color('purple'),
        ];
    }
}