<?php

namespace App\Filament\Resources\MutualTransfers;

use App\Models\MutualTransfer;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MutualTransferResource extends Resource
{
    protected static ?string $model = MutualTransfer::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedArrowsRightLeft;
    }

    public static function getNavigationGroup(): string
    {
        return 'Administration';
    }

    public static function getNavigationLabel(): string
    {
        return 'Mutual Transfers';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('mutual_transfers.view') || auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('phone')
                ->label('Contact Phone')
                ->required(),

            Select::make('preferred_division_id')
                ->label('Preferred Division')
                ->relationship('preferredDivision', 'name_en')
                ->searchable()
                ->nullable(),

            TextInput::make('preferred_subject')
                ->label('Preferred Subject')
                ->nullable(),

            Toggle::make('is_active')
                ->label('Active')
                ->helperText('Turn off once the transfer has been completed or the post is stale.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currentSchool.name_en')
                    ->label('Current School')
                    ->searchable(),

                TextColumn::make('currentSchool.division.name_en')
                    ->label('Current Division')
                    ->sortable(),

                TextColumn::make('preferredDivision.name_en')
                    ->label('Preferred Division')
                    ->placeholder('Any')
                    ->sortable(),

                TextColumn::make('preferred_subject')
                    ->label('Preferred Subject')
                    ->placeholder('Any'),

                TextColumn::make('phone')
                    ->label('Phone'),

                TextColumn::make('created_at')
                    ->label('Posted')
                    ->date('d M Y')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('preferred_division_id')
                    ->label('Preferred Division')
                    ->relationship('preferredDivision', 'name_en'),
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMutualTransfers::route('/'),
            'edit'  => Pages\EditMutualTransfer::route('/{record}/edit'),
        ];
    }
}
