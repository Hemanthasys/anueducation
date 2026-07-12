<?php

namespace App\Filament\Resources\ExpenditureVote;

use App\Filament\Resources\ExpenditureVote\Pages\CreateExpenditureVote;
use App\Filament\Resources\ExpenditureVote\Pages\EditExpenditureVote;
use App\Filament\Resources\ExpenditureVote\Pages\ListExpenditureVotes;
use App\Models\ExpenditureCategory;
use App\Models\ExpenditureVote;
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

class ExpenditureVoteResource extends Resource
{
    protected static ?string $model = ExpenditureVote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    public static function getNavigationGroup(): string
    {
        return 'Planning & Development';
    }

    public static function getNavigationLabel(): string
    {
        return 'Expenditure Votes';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('expenditure_votes.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('expenditure_votes.manage') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('expenditure_votes.manage') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('expenditure_votes.manage') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Expenditure Vote Details'))
                ->schema([
                    \Filament\Forms\Components\Select::make('expenditure_category_id')
                        ->label(__('Category'))
                        ->options(
                            ExpenditureCategory::where('is_active', true)
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
                        ->placeholder('e.g. REx1, CEx4')
                        ->maxLength(10),

                    \Filament\Forms\Components\Toggle::make('is_active')
                        ->label(__('Active'))
                        ->helperText(__('Inactive votes will not appear in project dropdowns but are kept for historical records.'))
                        ->default(true)
                        ->columnSpanFull(),

                    \Filament\Forms\Components\Textarea::make('label_si')
                        ->label(__('Label (Sinhala)'))
                        ->rows(3)
                        ->columnSpanFull(),

                    \Filament\Forms\Components\Textarea::make('label_en')
                        ->label(__('Label (English)'))
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
                    ->color(fn ($record) => match ($record->category?->code) {
                        'A'     => 'warning',
                        'B'     => 'success',
                        default => 'gray',
                    })
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
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('label_si')
                    ->label(__('Label (Sinhala)'))
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->label_si)
                    ->searchable()
                    ->placeholder('—'),

                ToggleColumn::make('is_active')
                    ->label(__('Active'))
                    ->disabled(fn () => ! auth()->user()?->can('expenditure_votes.manage')),

                TextColumn::make('projects_count')
                    ->label(__('Projects'))
                    ->counts('projects')
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                SelectFilter::make('expenditure_category_id')
                    ->label(__('Category'))
                    ->options(
                        ExpenditureCategory::all()->mapWithKeys(fn ($cat) => [
                            $cat->id => $cat->code . ' — ' . $cat->label_en,
                        ])
                    ),

                SelectFilter::make('is_active')
                    ->label(__('Status'))
                    ->options([
                        '1' => __('Active'),
                        '0' => __('Inactive / Reserved'),
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
            ->defaultSort('expenditure_category_id')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListExpenditureVotes::route('/'),
            'create' => CreateExpenditureVote::route('/create'),
            'edit'   => EditExpenditureVote::route('/{record}/edit'),
        ];
    }
}