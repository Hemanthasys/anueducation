<?php

namespace App\Filament\Resources\Teachers;

use App\Models\LookupValue;
use App\Models\Qualification;
use App\Models\School;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedAcademicCap;
    }

    public static function getNavigationGroup(): string
    {
        return 'School Management';
    }

    public static function getNavigationLabel(): string
    {
        return 'Teachers & Vice Principals';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([
            'super_admin', 'zonal_director', 'zonal_officer', 'divisional_director',
        ]);
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

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->nullable(),

                    FileUpload::make('photo')
                        ->label('Photo')
                        ->image()
                        ->disk('public')
                        ->directory('teacher-photos')
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

                    Select::make('subject_id')
                        ->label('Main Subject')
                        ->options(Subject::active()->pluck('name_en', 'id'))
                        ->searchable()
                        ->nullable(),

                    Select::make('user_id')
                        ->label('Login Account')
                        ->options(
                            User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))
                                ->whereNotNull('username')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->nullable()
                        ->helperText('Link to existing login account (optional)'),
                ]),

            Section::make('Employment Details')
                ->columns(2)
                ->schema([
                    Select::make('staff_type')
                        ->label('Staff Type')
                        ->options(LookupValue::optionsFor('staff_type'))
                        ->default('teacher')
                        ->required()
                        ->reactive(),

                    Select::make('appointment_type')
                        ->label('Appointment Type')
                        ->options(LookupValue::optionsFor('appointment_type'))
                        ->nullable(),

                    Select::make('service_grade')
                        ->label('Service Grade')
                        ->options(LookupValue::optionsFor('service_grade'))
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

            Section::make('Qualifications')
                ->schema([
                    Repeater::make('qualifications')
                        ->relationship('qualifications')
                        ->label('Qualifications')
                        ->schema([
                            Select::make('qualification_id')
                                ->label('Qualification')
                                ->options(Qualification::where('is_active', true)->pluck('name_en', 'id'))
                                ->required(),

                            Select::make('type')
                                ->label('Type')
                                ->options([
                                    'educational'  => 'Educational',
                                    'professional' => 'Professional',
                                ])
                                ->required(),

                            TextInput::make('year_obtained')
                                ->label('Year Obtained')
                                ->numeric()
                                ->minValue(1950)
                                ->maxValue(date('Y'))
                                ->nullable(),

                            TextInput::make('institution')
                                ->label('Institution')
                                ->maxLength(255)
                                ->nullable(),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add Qualification')
                        ->collapsible(),
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

                TextColumn::make('staff_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => $state === 'vice_principal' ? 'warning' : 'info')
                    ->formatStateUsing(fn($state) => LookupValue::labelFor('staff_type', $state)),

                TextColumn::make('service_grade')
                    ->label('Grade')
                    ->formatStateUsing(fn($state) => $state ? LookupValue::labelFor('service_grade', $state) : '—')
                    ->toggleable(),

                TextColumn::make('appointment_type')
                    ->label('Appointment')
                    ->formatStateUsing(fn($state) => $state ? LookupValue::labelFor('appointment_type', $state) : '—')
                    ->toggleable(),

                TextColumn::make('subject.name_en')
                    ->label('Subject')
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

                SelectFilter::make('staff_type')
                    ->label('Staff Type')
                    ->options(LookupValue::optionsFor('staff_type')),

                SelectFilter::make('appointment_type')
                    ->label('Appointment Type')
                    ->options(LookupValue::optionsFor('appointment_type')),

                SelectFilter::make('service_grade')
                    ->label('Service Grade')
                    ->options(LookupValue::optionsFor('service_grade')),

                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                EditAction::make(),

                Action::make('create_login')
                    ->label('Create Login')
                    ->icon(Heroicon::OutlinedKey)
                    ->color('success')
                    ->visible(fn(Teacher $record) => !$record->user_id)
                    ->requiresConfirmation()
                    ->modalHeading('Create Login Account')
                    ->modalDescription('This will create a login account for this teacher.')
                    ->action(function (Teacher $record) {
                        $username = strtolower(str_replace(' ', '.', $record->name)) . '.' . substr($record->nic ?? rand(1000, 9999), -4);
                        $password = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789'), 0, 8);

                        $user = User::create([
                            'name'                 => $record->name,
                            'username'             => $username,
                            'email'                => $record->email,
                            'phone'                => $record->phone,
                            'nic'                  => $record->nic,
                            'school_id'            => $record->school_id,
                            'password'             => Hash::make($password),
                            'must_change_password' => true,
                            'is_active'            => true,
                        ]);

                        $user->assignRole('teacher');
                        $record->update(['user_id' => $user->id]);

                        Notification::make()
                            ->title('Login created: ' . $username . ' / ' . $password)
                            ->success()
                            ->persistent()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('bulk_create_logins')
                        ->label('Create Logins for Selected')
                        ->icon(Heroicon::OutlinedKey)
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $results = [];
                            foreach ($records->where('user_id', null) as $teacher) {
                                $username = strtolower(str_replace(' ', '.', $teacher->name)) . '.' . substr($teacher->nic ?? rand(1000, 9999), -4);
                                $password = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789'), 0, 8);

                                $user = User::create([
                                    'name'                 => $teacher->name,
                                    'username'             => $username,
                                    'email'                => $teacher->email,
                                    'phone'                => $teacher->phone,
                                    'nic'                  => $teacher->nic,
                                    'school_id'            => $teacher->school_id,
                                    'password'             => Hash::make($password),
                                    'must_change_password' => true,
                                    'is_active'            => true,
                                ]);

                                $user->assignRole('teacher');
                                $teacher->update(['user_id' => $user->id]);
                                $results[] = $teacher->name . ' → ' . $username . ' / ' . $password;
                            }

                            Notification::make()
                                ->title(count($results) . ' logins created')
                                ->body(implode("\n", $results))
                                ->success()
                                ->persistent()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit'   => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
