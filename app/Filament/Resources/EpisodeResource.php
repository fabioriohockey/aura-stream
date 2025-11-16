<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use App\Filament\Components\VideoUpload;
use App\Filament\Components\ImageUpload;
use App\Models\Episode;
use App\Jobs\DownloadFromGoogleDrive;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $navigationIcon = 'heroicon-o-tv';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Conteúdo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Episódio')
                    ->schema([
                        Forms\Components\Select::make('dorama_id')
                            ->label('Dorama')
                            ->relationship('dorama', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('episode_number')
                            ->label('Número do Episódio')
                            ->numeric()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->maxLength(1000),

                        Forms\Components\TextInput::make('duration_seconds')
                            ->label('Duração (segundos)')
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('file_size_480p_mb')
                            ->label('Tamanho (480p) em MB')
                            ->numeric()
                            ->step(0.01)
                            ->default(0),

                        Forms\Components\TextInput::make('file_size_720p_mb')
                            ->label('Tamanho (720p) em MB')
                            ->numeric()
                            ->step(0.01),

                        Forms\Components\Select::make('video_format')
                            ->label('Formato do Vídeo')
                            ->options([
                                'webm' => 'WebM',
                                'mp4' => 'MP4',
                            ])
                            ->default('webm'),

                        Forms\Components\Select::make('video_codec')
                            ->label('Codec do Vídeo')
                            ->options([
                                'h265' => 'H.265',
                                'h264' => 'H.264',
                            ])
                            ->default('h265'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Arquivos de Vídeo')
                    ->schema([
                        Forms\Components\TextInput::make('drive_url_480p')
                            ->label('URL do Drive 480p')
                            ->url()
                            ->placeholder('https://drive.google.com/file/d/...')
                            ->helperText('Cole o link do Google Drive para o vídeo 480p'),

                        Forms\Components\TextInput::make('drive_url_720p')
                            ->label('URL do Drive 720p (Premium)')
                            ->url()
                            ->placeholder('https://drive.google.com/file/d/...')
                            ->helperText('Cole o link do Google Drive para o vídeo 720p'),

                        // Campos de URL do Drive
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Arquivos Locais')
                    ->schema([
                        Forms\Components\TextInput::make('video_path_480p')
                            ->label('Caminho do Vídeo 480p')
                            ->placeholder('doramas/videos/480p/nome_do_arquivo.mp4')
                            ->helperText('Preenchido automaticamente após download')
                            ->default(null),

                        Forms\Components\TextInput::make('video_path_720p')
                            ->label('Caminho do Vídeo 720p')
                            ->placeholder('doramas/videos/720p/nome_do_arquivo.mp4')
                            ->helperText('Preenchido automaticamente após download')
                            ->default(null),

                        ImageUpload::make('thumbnail_path')
                            ->label('Thumbnail')
                            ->directory('doramas/thumbnails')
                            ->helperText('Imagem de capa do episódio (16:9 recomendado)'),

                        Forms\Components\FileUpload::make('subtitles_path')
                            ->label('Legendas')
                            ->acceptedFileTypes(['text/vtt', 'application/x-subrip'])
                            ->directory('doramas/subtitles')
                            ->visibility('public')
                            ->maxSize(1024) // 1MB
                            ->helperText('Formato: VTT, SRT - Máx: 1MB'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Configurações')
                    ->schema([
                        Forms\Components\Toggle::make('is_premium_only')
                            ->label('Apenas para Premium')
                            ->default(false)
                            ->helperText('Apenas usuários premium podem assistir'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Ativo')
                            ->default(true)
                            ->helperText('Episódio visível no site'),

                        Forms\Components\DatePicker::make('air_date')
                            ->label('Data de Exibição')
                            ->displayFormat('d/m/Y')
                            ->native(false),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dorama.title')
                    ->label('Dorama')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('episode_number')
                    ->label('Episódio')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(80)
                    ->wrap(),

                Tables\Columns\ImageColumn::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->size(80)
                    ->circular(false)
                    ->defaultImageUrl(url('placeholder.svg')),

                Tables\Columns\TextColumn::make('duration_seconds')
                    ->label('Duração')
                    ->formatStateUsing(fn (int $state): string => gmdate('i:s', $state))
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('file_size_480p_mb')
                    ->label('Tamanho 480p')
                    ->formatStateUsing(fn ($state): string => $state ? $state . ' MB' : '-')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('file_size_720p_mb')
                    ->label('Tamanho 720p')
                    ->formatStateUsing(fn ($state): string => $state ? $state . ' MB' : '-')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_premium_only')
                    ->label('Premium')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-gift')
                    ->trueColor('warning')
                    ->falseColor('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Visualizações')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('drive_url_480p')
                    ->label('URL Drive 480p')
                    ->limit(30)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('drive_url_720p')
                    ->label('URL Drive 720p')
                    ->limit(30)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dorama_id')
                    ->label('Dorama')
                    ->relationship('dorama', 'title')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('is_premium_only')
                    ->label('Tipo')
                    ->options([
                        1 => 'Apenas Premium',
                        0 => 'Todos',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download_480p')
                    ->label('Baixar 480p')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Episode $record) {
                        if (!$record->drive_url_480p) {
                            \Filament\Notifications\Notification::make()
                                ->title('Erro')
                                ->body('URL do Drive 480p não configurada.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $filename = "video_480p_" . $record->id . "_" . time() . ".mp4";
                        $path = "doramas/videos/480p/{$filename}";

                        DownloadFromGoogleDrive::dispatch($record->drive_url_480p, $path, $record->id, '480');

                        \Filament\Notifications\Notification::make()
                            ->title('Download iniciado!')
                            ->body('O vídeo 480p está sendo baixado em segundo plano.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('download_720p')
                    ->label('Baixar 720p')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Episode $record) {
                        if (!$record->drive_url_720p) {
                            \Filament\Notifications\Notification::make()
                                ->title('Erro')
                                ->body('URL do Drive 720p não configurada.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $filename = "video_720p_" . $record->id . "_" . time() . ".mp4";
                        $path = "doramas/videos/720p/{$filename}";

                        DownloadFromGoogleDrive::dispatch($record->drive_url_720p, $path, $record->id, '720');

                        \Filament\Notifications\Notification::make()
                            ->title('Download iniciado!')
                            ->body('O vídeo 720p está sendo baixado em segundo plano.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('test_stream')
                    ->label('Testar Streaming')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->url(fn (Episode $record): string => 'https://dorama.vortexsistemas.dev/api/test-stream/' . $record->id . '?quality=720p')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Ativar Selecionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_active' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desativar Selecionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_active' => false]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('make_premium')
                        ->label('Marcar como Premium')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_premium_only' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('Nenhum episódio encontrado')
            ->emptyStateDescription('Comece cadastrando episódios para seus doramas.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Novo Episódio'),
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
            'index' => Pages\ListEpisodes::route('/'),
            'create' => Pages\CreateEpisode::route('/create'),
            'view' => Pages\ViewEpisode::route('/{record}'),
            'edit' => Pages\EditEpisode::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}