<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoramaResource\Pages;
use App\Models\Dorama;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DoramaResource extends Resource
{
    protected static ?string $model = Dorama::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Conteúdo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->required()
                    ->rows(3),

                Forms\Components\Textarea::make('synopsis')
                    ->label('Sinopse')
                    ->rows(3),

                Forms\Components\Select::make('country')
                    ->label('País')
                    ->options([
                        'Coreia' => 'Coreia',
                        'Japão' => 'Japão',
                        'China' => 'China',
                        'Tailândia' => 'Tailândia',
                        'Brasil' => 'Brasil',
                        'Portugal' => 'Portugal',
                        'Estados Unidos' => 'Estados Unidos',
                        'Reino Unido' => 'Reino Unido',
                        'França' => 'França',
                        'Alemanha' => 'Alemanha',
                        'Itália' => 'Itália',
                        'Espanha' => 'Espanha',
                        'Canadá' => 'Canadá',
                        'Austrália' => 'Austrália',
                        'México' => 'México',
                        'Argentina' => 'Argentina',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('year')
                    ->label('Ano')
                    ->numeric()
                    ->required()
                    ->minValue(1900)
                    ->maxValue(date('Y') + 5),

                Forms\Components\TextInput::make('episodes_total')
                    ->label('Total de Episódios')
                    ->numeric()
                    ->required()
                    ->default(1),

                Forms\Components\TextInput::make('duration_minutes')
                    ->label('Duração (minutos)')
                    ->numeric()
                    ->default(45),

                Forms\Components\FileUpload::make('poster_path')
                    ->label('Poster')
                    ->image()
                    ->directory('doramas/posters')
                    ->maxSize(10240) // 10MB
                    ->helperText('Imagem do poster (Máx: 10MB)'),

                Forms\Components\FileUpload::make('backdrop_path')
                    ->label('Backdrop')
                    ->image()
                    ->directory('doramas/backdrops')
                    ->maxSize(10240) // 10MB
                    ->helperText('Imagem de fundo (Máx: 10MB)'),

                Forms\Components\TextInput::make('trailer_url')
                    ->label('URL do Trailer')
                    ->url()
                    ->helperText('URL do YouTube ou vídeo local'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'em_exibicao' => 'Em Exibição',
                        'finalizado' => 'Finalizado',
                        'cancelado' => 'Cancelado',
                    ])
                    ->default('finalizado')
                    ->required(),

                Forms\Components\TextInput::make('rating')
                    ->label('Avaliação')
                    ->numeric()
                    ->step(0.1)
                    ->minValue(0)
                    ->maxValue(9.9)
                    ->default(0),

                Forms\Components\Toggle::make('is_featured')
                    ->label('Destaque')
                    ->default(false),

                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')
                    ->default(true),

                Forms\Components\DatePicker::make('release_date')
                    ->label('Data de Lançamento')
                    ->displayFormat('d/m/Y')
                    ->native(false),

                Forms\Components\Select::make('language')
                    ->label('Idioma')
                    ->options([
                        'ko' => 'Coreano',
                        'ja' => 'Japonês',
                        'zh' => 'Chinês',
                        'th' => 'Tailandês',
                        'pt' => 'Português',
                        'pt-br' => 'Português (BR)',
                        'en' => 'Inglês',
                    ])
                    ->default('pt-br'),

                Forms\Components\TextInput::make('imdb_id')
                    ->label('IMDB ID')
                    ->helperText('ID do IMDB (opcional)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\ImageColumn::make('poster_path')
                    ->label('Poster')
                    ->size(60)
                    ->circular(false)
                    ->defaultImageUrl(url('placeholder.svg')),

                Tables\Columns\TextColumn::make('country')
                    ->label('País')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('year')
                    ->label('Ano')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('episodes_total')
                    ->label('Episódios')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Avaliação')
                    ->formatStateUsing(fn ($state): string => $state ? $state . ' ⭐' : '-')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'em_exibicao' => 'warning',
                        'finalizado' => 'success',
                        'cancelado' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'em_exibicao' => 'Em Exibição',
                        'finalizado' => 'Finalizado',
                        'cancelado' => 'Cancelado',
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destaque')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray'),

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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->label('País')
                    ->options([
                        'Coreia' => 'Coreia',
                        'Japão' => 'Japão',
                        'China' => 'China',
                        'Tailândia' => 'Tailândia',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'em_exibicao' => 'Em Exibição',
                        'finalizado' => 'Finalizado',
                        'cancelado' => 'Cancelado',
                    ]),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destaque')
                    ->placeholder('Todos')
                    ->trueLabel('Com destaque')
                    ->falseLabel('Sem destaque'),

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
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Marcar como Destaque')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each->update(['is_featured' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
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
            'index' => Pages\ListDoramas::route('/'),
            'create' => Pages\CreateDorama::route('/create'),
            'view' => Pages\ViewDorama::route('/{record}'),
            'edit' => Pages\EditDorama::route('/{record}/edit'),
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