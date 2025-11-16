<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WatchHistoryResource\Pages;
use App\Models\WatchHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WatchHistoryResource extends Resource
{
    protected static ?string $model = WatchHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Relatórios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Histórico')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Usuário')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('episode_id')
                            ->label('Episódio')
                            ->relationship('episode', function ($query) {
                                return $query->with('dorama');
                            })
                            ->getOptionLabelUsing(function ($record) {
                                return $record->dorama->title . ' - Episódio ' . $record->episode_number;
                            })
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('progress_seconds')
                            ->label('Progresso (segundos)')
                            ->numeric()
                            ->required()
                            ->helperText('Quantos segundos o usuário já assistiu'),

                        Forms\Components\Toggle::make('is_completed')
                            ->label('Concluído')
                            ->helperText('Se o episódio foi assistido até o fim'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Dados do Sistema')
                    ->schema([
                        Forms\Components\DateTimePicker::make('watched_at')
                            ->label('Data/Hora da Visualização')
                            ->required()
                            ->displayFormat('d/m/Y H:i:s')
                            ->withoutSeconds(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('episode.dorama.title')
                    ->label('Dorama')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('episode.episode_number')
                    ->label('Episódio')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('episode.title')
                    ->label('Título do Episódio')
                    ->searchable()
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\TextColumn::make('progress_seconds')
                    ->label('Progresso')
                    ->formatStateUsing(fn (int $state): string => gmdate('H:i:s', $state))
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progresso %')
                    ->getStateUsing(function (Episode $record, $state): float {
                        if ($record->duration_seconds > 0) {
                            return round(($state / $record->duration_seconds) * 100, 1);
                        }
                        return 0;
                    })
                    ->formatStateUsing(fn (float $state): string => $state . '%')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_completed')
                    ->label('Concluído')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('episode.duration_seconds')
                    ->label('Duração Total')
                    ->formatStateUsing(fn (int $state): string => gmdate('H:i:s', $state))
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('watched_at')
                    ->label('Assistido em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('episode_id')
                    ->label('Episódio')
                    ->relationship('episode', function ($query) {
                        return $query->with('dorama');
                    })
                    ->getOptionLabelUsing(function ($record) {
                        return $record->dorama->title . ' - Episódio ' . $record->episode_number;
                    })
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('is_completed')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Concluídos')
                    ->falseLabel('Em progresso'),
                Tables\Filters\Filter::make('recent')
                    ->label('Recentes')
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label('Data')
                            ->date()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['date'],
                            fn (Builder $query, $date): Builder => $query->whereDate('watched_at', $date)
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('watch_episode')
                    ->label('Assistir')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->url(fn (WatchHistory $record): string => '/api/stream/' . $record->episode_id)
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_completed')
                        ->label('Marcar como Concluídos')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_completed' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('mark_in_progress')
                        ->label('Marcar como Em Progresso')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_completed' => false]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('Nenhum histórico encontrado')
            ->emptyStateDescription('Os históricos de visualização aparecerão aqui quando os usuários assistirem aos episódios.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create_sample')
                    ->label('Criar Exemplo')
                    ->icon('heroicon-o-plus')
                    ->action(function () {
                        // Criar alguns exemplos de histórico
                        // Você pode implementar isso depois
                    })
                    ->color('primary'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWatchHistories::route('/'),
            'create' => Pages\CreateWatchHistory::route('/create'),
            'view' => Pages\ViewWatchHistory::route('/{record}'),
            'edit' => Pages\EditWatchHistory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}