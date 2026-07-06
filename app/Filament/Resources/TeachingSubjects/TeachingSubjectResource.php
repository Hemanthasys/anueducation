<?php

namespace App\Filament\Resources\TeachingSubjects;

use App\Models\TeachingSubject;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeachingSubjectResource extends Resource
{
    protected static ?string $model = TeachingSubject::class;

    private const LEVEL_OPTIONS = [
        'primary' => 'Primary (Grades 1-5)',
        'middle'  => 'Middle (Grades 6-9)',
        'ol'      => 'O/L (Grades 10-11)',
        'al'      => 'A/L (Grades 12-13)',
        'all'     => 'All Levels',
    ];

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedBookOpen;
    }

    public static function getNavigationGroup(): string
    {
        return 'User Management';
    }

    public static function getNavigationLabel(): string
    {
        return 'Teaching Subjects';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('settings.lookup_values') || auth()->user()->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Teaching Subject')
                ->columns(2)
                ->schema([
                    TextInput::make('name_en')
                        ->label('Name (English)')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('name_si')
                        ->label('Name (Sinhala)')
                        ->maxLength(255),

                    Select::make('level')
                        ->label('Level')
                        ->options(self::LEVEL_OPTIONS)
                        ->required()
                        ->default('all'),

                    TextInput::make('order')
                        ->label('Display Order')
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_en')
                    ->label('English Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_si')
                    ->label('Sinhala Name')
                    ->searchable(),

                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => self::LEVEL_OPTIONS[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'primary' => 'success',
                        'middle'  => 'info',
                        'ol'      => 'warning',
                        'al'      => 'danger',
                        default   => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('order')
                    ->label('Order')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('level')
                    ->options(self::LEVEL_OPTIONS),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTeachingSubjects::route('/'),
            'create' => Pages\CreateTeachingSubject::route('/create'),
            'edit'   => Pages\EditTeachingSubject::route('/{record}/edit'),
        ];
    }
}
