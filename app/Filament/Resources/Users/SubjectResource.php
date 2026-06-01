<?php

namespace App\Filament\Resources\Subjects;

use App\Models\Subject;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedBookOpen;
    }

    public static function getNavigationGroup(): string
    {
        return 'User Management';
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
            TextInput::make('name_en')
                ->label('Name (English)')
                ->required()
                ->maxLength(255),

            TextInput::make('name_si')
                ->label('Name (Sinhala)')
                ->maxLength(255),

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
                TextColumn::make('order')->sortable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->reorderable('order')
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit'   => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
