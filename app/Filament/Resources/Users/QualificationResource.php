<?php

namespace App\Filament\Resources\Qualifications;

use App\Models\Qualification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QualificationResource extends Resource
{
    protected static ?string $model = Qualification::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedAcademicCap;
    }

    public static function getNavigationGroup(): string
    {
        return 'User Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name_en')
                ->label('Name (English)')
                ->required()
                ->maxLength(255),

            TextInput::make('name_si')
                ->label('Name (Sinhala)')
                ->maxLength(255),

            Select::make('type')
                ->options([
                    'educational'  => 'Educational',
                    'professional' => 'Professional',
                ])
                ->required()
                ->default('educational'),

            TextInput::make('order')
                ->label('Display Order')
                ->numeric()
                ->default(0),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_en')->label('English Name')->searchable()->sortable(),
                TextColumn::make('name_si')->label('Sinhala Name')->searchable(),
                TextColumn::make('type')->badge()
                    ->color(fn($state) => $state === 'educational' ? 'info' : 'success'),
                TextColumn::make('order')->sortable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([
                SelectFilter::make('type')->options([
                    'educational'  => 'Educational',
                    'professional' => 'Professional',
                ]),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->reorderable('order')
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQualifications::route('/'),
            'create' => Pages\CreateQualification::route('/create'),
            'edit'   => Pages\EditQualification::route('/{record}/edit'),
        ];
    }
}
