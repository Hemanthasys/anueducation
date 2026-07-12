<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Models\Event;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Website Content';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Events Calendar';

    public static function canAccess(): bool
    {
        return auth()->user()->can('content.events') || auth()->user()->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Event Details')
                ->schema([
                    Forms\Components\TextInput::make('title_en')
                        ->label('Title (English)')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('title_si')
                        ->label('Title (Sinhala)')
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\Textarea::make('description_en')
                        ->label('Description (English)')
                        ->rows(3)
                        ->columnSpan(1),
                    Forms\Components\Textarea::make('description_si')
                        ->label('Description (Sinhala)')
                        ->rows(3)
                        ->columnSpan(1),
                ])
                ->columns(2),

            Section::make('Date & Time')
                ->schema([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->native(false)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($state && !$get('end_date')) {
                                $set('end_date', $state);
                            }
                        }),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->native(false)
                        ->minDate(fn (callable $get) => $get('start_date'))
                        ->helperText('Same as start date for single-day events'),
                    Forms\Components\TimePicker::make('start_time')
                        ->label('Start Time')
                        ->seconds(false)
                        ->helperText('Leave empty for all-day event'),
                    Forms\Components\TimePicker::make('end_time')
                        ->label('End Time')
                        ->seconds(false),
                ])
                ->columns(2),

            Section::make('Additional Info')
                ->schema([
                    Forms\Components\TextInput::make('location')
                        ->label('Location')
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\Select::make('color')
                        ->label('Calendar Color')
                        ->options([
                            'primary' => 'Primary (Theme Color)',
                            'accent'  => 'Accent (Theme Color)',
                            'red'     => 'Red',
                            'green'   => 'Green',
                            'orange'  => 'Orange',
                            'purple'  => 'Purple',
                        ])
                        ->default('primary')
                        ->required()
                        ->columnSpan(1),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active (visible on website)')
                        ->default(true)
                        ->columnSpan(2),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title_en')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Time')
                    ->time('H:i')
                    ->placeholder('All day'),
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->limit(30)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('color')
                    ->label('Color')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\Filter::make('upcoming')
                    ->query(fn (Builder $query) => $query->where('end_date', '>=', now()))
                    ->label('Upcoming Only'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit'   => EditEvent::route('/{record}/edit'),
        ];
    }
}