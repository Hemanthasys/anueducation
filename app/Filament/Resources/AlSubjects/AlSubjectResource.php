<?php

namespace App\Filament\Resources\AlSubjects;

use App\Models\AlSubject;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AlSubjectResource extends Resource
{
    protected static ?string $model = AlSubject::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedAcademicCap;
    }

    public static function getNavigationGroup(): string
    {
        return 'Reference Data';
    }

    public static function getNavigationLabel(): string
    {
        return 'A/L Subjects';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('settings.lookup_values') || auth()->user()->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('A/L Subject')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Subject Code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(10),

                    TextInput::make('name_en')
                        ->label('Name (English)')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('name_si')
                        ->label('Name (Sinhala)')
                        ->maxLength(255),

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
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_en')
                    ->label('English Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_si')
                    ->label('Sinhala Name')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('code');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAlSubjects::route('/'),
            'create' => Pages\CreateAlSubject::route('/create'),
            'edit'   => Pages\EditAlSubject::route('/{record}/edit'),
        ];
    }
}
