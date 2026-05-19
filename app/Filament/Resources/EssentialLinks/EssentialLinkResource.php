<?php

namespace App\Filament\Resources\EssentialLinks;

use App\Filament\Resources\EssentialLinks\Pages\CreateEssentialLink;
use App\Filament\Resources\EssentialLinks\Pages\EditEssentialLink;
use App\Filament\Resources\EssentialLinks\Pages\ListEssentialLinks;
use App\Models\EssentialLink;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EssentialLinkResource extends Resource
{
    protected static ?string $model = EssentialLink::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    public static function getNavigationLabel(): string { return 'Essential Links'; }
    public static function getNavigationGroup(): string { return 'Website Content'; }
    public static function getNavigationSort(): ?int    { return 6; }
    public static function getPluralLabel(): string     { return 'Essential Links'; }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Link Identity')
                    ->description('Name displayed on the homepage card.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name_en')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(150),

                        TextInput::make('name_si')
                            ->label('Name (Sinhala) / නම')
                            ->required()
                            ->maxLength(150),
                    ]),

                Section::make('Description')
                    ->description('Short description shown on the card. Keep it under 2 lines.')
                    ->columns(2)
                    ->schema([
                        Textarea::make('description_en')
                            ->label('Description (English)')
                            ->rows(3)
                            ->maxLength(300),

                        Textarea::make('description_si')
                            ->label('Description (Sinhala) / විස්තරය')
                            ->rows(3)
                            ->maxLength(300),
                    ]),

                Section::make('Logo')
                    ->description('Recommended: 200 × 200 px square | PNG with transparent background | Max 500 KB | Formats: PNG, JPG, WebP, SVG.')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo Image')
                            ->image()
                            ->disk('public')
                            ->directory('essential-links')
                            ->maxSize(512)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                            ->imagePreviewHeight('120')
                            ->columnSpanFull(),
                    ]),

                Section::make('Link & Settings')
                    ->columns(2)
                    ->schema([
                        TextInput::make('url')
                            ->label('Website URL')
                            ->required()
                            ->url()
                            ->placeholder('https://...')
                            ->maxLength(500)
                            ->columnSpanFull(),

                        TextInput::make('order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower number appears first.'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive links are hidden from the homepage.'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->height(40)
                    ->width(40),

                TextColumn::make('name_en')
                    ->label('Name (EN)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name_si')
                    ->label('Name (SI)')
                    ->searchable(),

                TextColumn::make('url')
                    ->label('URL')
                    ->limit(40),

                TextColumn::make('order')
                    ->label('Order')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('order')
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEssentialLinks::route('/'),
            'create' => CreateEssentialLink::route('/create'),
            'edit'   => EditEssentialLink::route('/{record}/edit'),
        ];
    }
}