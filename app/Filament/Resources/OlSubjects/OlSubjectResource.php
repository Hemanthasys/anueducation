<?php

namespace App\Filament\Resources\OlSubjects;

use App\Models\OlSubject;
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

class OlSubjectResource extends Resource
{
    protected static ?string $model = OlSubject::class;

    private const SUBJECT_GROUPS = [
        'religion'   => 'Religion',
        'core'       => 'Core',
        'category1'  => 'Category I',
        'category2'  => 'Category II',
        'category3'  => 'Category III',
    ];

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
        return 'O/L Subjects';
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
            Section::make('O/L Subject')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Subject Code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(5),

                    Select::make('subject_group')
                        ->label('Subject Group')
                        ->options(self::SUBJECT_GROUPS)
                        ->required(),

                    TextInput::make('name_en')
                        ->label('Name (English)')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('name_si')
                        ->label('Name (Sinhala)')
                        ->maxLength(255),

                    Toggle::make('is_mother_language')
                        ->label('Is Mother Language Subject')
                        ->default(false),

                    Toggle::make('is_mathematics')
                        ->label('Is Mathematics Subject')
                        ->default(false),

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

                TextColumn::make('subject_group')
                    ->label('Group')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => self::SUBJECT_GROUPS[$state] ?? $state)
                    ->sortable(),

                IconColumn::make('is_mother_language')
                    ->label('Mother Lang.')
                    ->boolean(),

                IconColumn::make('is_mathematics')
                    ->label('Maths')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('subject_group')
                    ->options(self::SUBJECT_GROUPS),
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
            'index'  => Pages\ListOlSubjects::route('/'),
            'create' => Pages\CreateOlSubject::route('/create'),
            'edit'   => Pages\EditOlSubject::route('/{record}/edit'),
        ];
    }
}
