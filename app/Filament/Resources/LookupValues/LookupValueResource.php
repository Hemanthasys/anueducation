<?php

namespace App\Filament\Resources\LookupValues;

use App\Models\LookupValue;
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
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class LookupValueResource extends Resource
{
    protected static ?string $model = LookupValue::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedListBullet;
    }

    public static function getNavigationGroup(): string
    {
        return 'Reference Data';
    }

    public static function getNavigationLabel(): string
    {
        return 'Lookup Values';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    // Only super_admin
    public static function canAccess(): bool
    {
       return auth()->user()->can('settings.lookup_values') || auth()->user()->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lookup Value')
                ->columns(2)
                ->schema([
                    Select::make('category')
                        ->label('Category')
                        ->options([
                            'appointment_type'  => 'Appointment Type',
                            'service_grade'     => 'Service Grade',
                            'staff_type'        => 'Staff Type',
                            'non_academic_role' => 'Non-Academic Role',
                        ])
                        ->required(),

                    TextInput::make('value')
                        ->label('Value (code)')
                        ->required()
                        ->maxLength(50)
                        ->helperText('Unique code e.g. SLTS_I, permanent'),

                    TextInput::make('label_en')
                        ->label('Label (English)')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('label_si')
                        ->label('Label (Sinhala)')
                        ->maxLength(100),

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
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'appointment_type'  => 'info',
                        'service_grade'     => 'success',
                        'staff_type'        => 'warning',
                        'non_academic_role' => 'danger',
                        default             => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('value')
                    ->label('Value')
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('label_en')
                    ->label('English')
                    ->searchable(),

                TextColumn::make('label_si')
                    ->label('Sinhala')
                    ->searchable(),

                TextColumn::make('order')
                    ->label('Order')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'appointment_type'  => 'Appointment Type',
                        'service_grade'     => 'Service Grade',
                        'staff_type'        => 'Staff Type',
                        'non_academic_role' => 'Non-Academic Role',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('category')
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLookupValues::route('/'),
            'create' => Pages\CreateLookupValue::route('/create'),
            'edit'   => Pages\EditLookupValue::route('/{record}/edit'),
        ];
    }
}
