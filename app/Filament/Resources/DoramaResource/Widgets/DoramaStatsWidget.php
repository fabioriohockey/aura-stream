<?php

namespace App\Filament\Resources\DoramaResource\Widgets;

use App\Models\Dorama;
use App\Models\Episode;
use App\Models\Category;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DoramaStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $totalDoramas = Dorama::count();
        $activeDoramas = Dorama::where('is_active', true)->count();
        $premiumDoramas = Dorama::where('is_premium', true)->count();
        $featuredDoramas = Dorama::where('featured', true)->count();
        $totalEpisodes = Episode::count();
        $totalCategories = Category::count();

        // Calculate by status
        $ongoingCount = Dorama::where('status', 'ongoing')->count();
        $completedCount = Dorama::where('status', 'completed')->count();
        $cancelledCount = Dorama::where('status', 'cancelled')->count();

        // Calculate by type
        $seriesCount = Dorama::where('type', 'series')->count();
        $movieCount = Dorama::where('type', 'movie')->count();
        $specialCount = Dorama::where('type', 'special')->count();

        return [
            Stat::make('Total de Doramas', $totalDoramas)
                ->description($activeDoramas . ' ativos')
                ->descriptionIcon('heroicon-m-film')
                ->chart([7, 12, 10, 14, 15, 18, 20])
                ->color('primary'),

            Stat::make('Doramas Premium', $premiumDoramas)
                ->description(round(($premiumDoramas / max($totalDoramas, 1)) * 100, 1) . '% do total')
                ->descriptionIcon('heroicon-m-crown')
                ->chart([2, 4, 3, 6, 8, 9, 11])
                ->color('warning'),

            Stat::make('Destaques', $featuredDoramas)
                ->description('Em destaque na home')
                ->descriptionIcon('heroicon-m-star')
                ->chart([1, 3, 2, 4, 3, 5, 4])
                ->color('info'),

            Stat::make('Total de Episódios', $totalEpisodes)
                ->description(round($totalEpisodes / max($totalDoramas, 1), 1) . ' episódios/dorama')
                ->descriptionIcon('heroicon-m-tv')
                ->chart([50, 120, 180, 240, 300, 380, 450])
                ->color('success'),

            Stat::make('Em Lançamento', $ongoingCount)
                ->description('Ativos no momento')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([5, 8, 6, 9, 11, 13, 15])
                ->color('indigo'),

            Stat::make('Categorias', $totalCategories)
                ->description('Gêneros disponíveis')
                ->descriptionIcon('heroicon-m-tag')
                ->color('purple'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}