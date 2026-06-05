<?php

namespace App\Filament\Resources\FundingSource;

use App\Filament\Resources\FundingSource\Pages\CreateFundingSource;
use App\Filament\Resources\FundingSource\Pages\EditFundingSource;
use App\Filament\Resources\FundingSource\Pages\ListFundingSources;
use App\Models\FundingCategory;
use App\Models\FundingSource;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FundingSourceResource extends Resource
{
    protected static ?string $model = FundingSource::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getNavigationGroup(): string
    {
        return 'Planning & Development';
    }

    public static function getNavigationLabel(): string
    {
        return 'Funding Sources';
    }

    public static function getNavigationSort(): ?int
    {
        return 20;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('funding_sources.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('funding_sources.manage') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('funding_sources.manage') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('funding_sources.manage') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Funding Source Details'))
                ->schema([
                    \Filament\Forms\Components\Select::make('funding_category_id')
                        ->label(__('Category'))
                        ->options(
                            FundingCategory::where('is_active', true)
                                ->get()
                                ->mapWithKeys(fn ($cat) => [
                                    $cat->id => $cat->code . ' — ' . $cat->label_en,
                                ])
                        )
                        ->required()
                        ->searchable()
                        ->columnSpanFull(),

                    \Filament\Forms\Components\TextInput::make('code')
                        ->label(__('Code'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('e.g. S1, S2')
                        ->maxLength(10),

                    \Filament\Forms\Components\Toggle::make('is_active')
                        ->label(__('Active'))
                        ->helperText(__('Inactive sources will not appear in project dropdowns but are kept for historical records.'))
                        ->default(true)
                        ->columnSpanFull(),

                    \Filament\Forms\Components\Textarea::make('label_si')
                        ->label(__('Label (Sinhala)'))
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    \Filament\Forms\Components\Textarea::make('label_en')
                        ->label(__('Label (English)'))
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.code')
                    ->label(__('Category'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('code')
                    ->label(__('Code'))
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('label_en')
                    ->label(__('Label (English)'))
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->label_en)
                    ->searchable(),

                TextColumn::make('label_si')
                    ->label(__('Label (Sinhala)'))
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->label_si)
                    ->searchable(),

                ToggleColumn::make('is_active')
                    ->label(__('Active'))
                    ->disabled(fn () => ! auth()->user()?->can('funding_sources.manage')),

                TextColumn::make('projects_count')
                    ->label(__('Projects'))
                    ->counts('projects')
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                SelectFilter::make('funding_category_id')
                    ->label(__('Category'))
                    ->options(
                        FundingCategory::all()->mapWithKeys(fn ($cat) => [
                            $cat->id => $cat->code . ' — ' . $cat->label_en,
                        ])
                    ),

                SelectFilter::make('is_active')
                    ->label(__('Status'))
                    ->options([
                        '1' => __('Active'),
                        '0' => __('Inactive'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('funding_category_id')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFundingSources::route('/'),
            'create' => CreateFundingSource::route('/create'),
            'edit'   => EditFundingSource::route('/{record}/edit'),
        ];
    }
}