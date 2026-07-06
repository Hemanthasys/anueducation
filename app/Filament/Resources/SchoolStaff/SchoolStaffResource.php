<?php

namespace App\Filament\Resources\SchoolStaff;

use App\Models\LookupValue;
use App\Models\School;
use App\Models\SchoolStaff;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use App\Filament\Traits\HasViewManagePermissions;

class SchoolStaffResource extends Resource
{
    use HasViewManagePermissions;
    protected static string $viewPermission   = 'staff.view';
    protected static string $managePermission = 'staff.manage';

    protected static ?string $model = SchoolStaff::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedIdentification;
    }

    public static function getNavigationGroup(): string
    {
        return 'School Management';
    }

    public static function getNavigationLabel(): string
    {
        return 'Non-Academic Staff';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Basic Information')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Full Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('nic')
                        ->label('NIC Number')
                        ->maxLength(12)
                        ->nullable(),

                    Select::make('gender')
                        ->label('Gender')
                        ->options(['M' => 'Male / පිරිමි', 'F' => 'Female / ගැහැණු'])
                        ->nullable(),

                    DatePicker::make('birthday')
                        ->label('Date of Birth')
                        ->nullable(),

                    TextInput::make('phone')
                        ->label('Phone')
                        ->tel()
                        ->maxLength(15)
                        ->nullable(),

                    FileUpload::make('photo')
                        ->label('Photo')
                        ->image()
                        ->disk('public')
                        ->directory('staff-photos')
                        ->nullable()
                        ->columnSpanFull(),
                ]),

            Section::make('School Assignment')
                ->columns(2)
                ->schema([
                    Select::make('school_id')
                        ->label('School')
                        ->options(School::where('is_active', true)->orderBy('name_en')->pluck('name_en', 'id'))
                        ->searchable()
                        ->required(),
                ]),

            Section::make('Employment Details')
                ->columns(2)
                ->schema([
                    Select::make('non_academic_role')
                        ->label('Role')
                        ->options(LookupValue::optionsFor('non_academic_role'))
                        ->required(),

                    Select::make('appointment_type')
                        ->label('Appointment Type')
                        ->options(LookupValue::optionsFor('appointment_type'))
                        ->nullable(),

                    TextInput::make('salary_slip_no')
                        ->label('Salary Slip No.')
                        ->maxLength(50)
                        ->nullable(),

                    TextInput::make('designation')
                        ->label('Designation')
                        ->maxLength(100)
                        ->nullable(),

                    DatePicker::make('appointed_date')
                        ->label('First Appointed Date')
                        ->nullable(),

                    DatePicker::make('joined_school_date')
                        ->label('Joined This School')
                        ->nullable(),

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
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->nic),

                TextColumn::make('school.name_en')
                    ->label('School')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('non_academic_role')
                    ->label('Role')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn($state) => $state ? LookupValue::labelFor('non_academic_role', $state) : '—'),

                TextColumn::make('appointment_type')
                    ->label('Appointment')
                    ->formatStateUsing(fn($state) => $state ? LookupValue::labelFor('appointment_type', $state) : '—')
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('school_id')
                    ->label('School')
                    ->options(School::where('is_active', true)->orderBy('name_en')->pluck('name_en', 'id'))
                    ->searchable(),

                SelectFilter::make('non_academic_role')
                    ->label('Role')
                    ->options(LookupValue::optionsFor('non_academic_role')),

                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                EditAction::make()
                ->visible(fn () => auth()->user()?->can('staff.manage') || auth()->user()?->hasRole('super_admin')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('staff.manage') || auth()->user()?->hasRole('super_admin')),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSchoolStaff::route('/'),
            'create' => Pages\CreateSchoolStaff::route('/create'),
            'edit'   => Pages\EditSchoolStaff::route('/{record}/edit'),
        ];
    }
}
