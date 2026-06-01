<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Division;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return Heroicon::OutlinedUsers;
    }

    public static function getNavigationGroup(): string
    {
        return 'User Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

   public static function canAccess(): bool
    {
        return auth()->user()->can('users.view') || auth()->user()->hasRole('super_admin');
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

                    TextInput::make('username')
                        ->label('Username')
                        ->unique(ignoreRecord: true)
                        ->helperText('Auto-generated on save if left blank')
                        ->maxLength(50),

                    TextInput::make('nic')
                        ->label('NIC Number')
                        ->maxLength(12),

                    DatePicker::make('birthday')
                        ->label('Date of Birth')
                        ->maxDate(now()->subYears(18)),

                    TextInput::make('email')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(15),
                ]),

            Section::make('Role & Assignment')
                ->columns(2)
                ->schema([
                    Select::make('roles')
                        ->label('Role')
                        ->relationship('roles', 'name', fn($query) => $query->whereNotIn('name', ['teacher', 'public']))
                        ->preload()
                        ->required()
                        ->searchable()
                        ->helperText('Teacher & Vice Principal accounts are created via Teacher Management using the Create Login button.'),

                    Select::make('school_id')
                        ->label('School')
                        ->options(School::orderBy('name_en')->pluck('name_en', 'id'))
                        ->searchable()
                        ->nullable()
                        ->helperText('Required for principal and teacher roles'),

                    Select::make('division_id')
                        ->label('Division')
                        ->options(Division::orderBy('name_en')->pluck('name_en', 'id'))
                        ->searchable()
                        ->nullable()
                        ->helperText('Required for divisional director role'),

                ]),

            Section::make('Employment')
                ->columns(2)
                ->schema([
                    DatePicker::make('appointed_date')
                        ->label('First Appointed Date')
                        ->maxDate(today()),

                    TextInput::make('designation')
                        ->label('Designation')
                        ->maxLength(100)
                        ->nullable(),

                    FileUpload::make('photo')
                        ->label('Profile Photo')
                        ->image()
                        ->disk('public')
                        ->directory('user-photos')
                        ->maxSize(2048)
                        ->nullable(),
                ]),

            Section::make('Account Settings')
                ->columns(2)
                ->schema([
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->dehydrated(fn($state) => filled($state))
                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                        ->helperText('Leave blank to keep existing password'),

                    Toggle::make('must_change_password')
                        ->label('Force Password Change on Next Login')
                        ->default(true),

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
                    ->description(fn($record) => $record->username),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->separator(','),

                TextColumn::make('school.name_en')
                    ->label('School')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->toggleable(),

                IconColumn::make('must_change_password')
                    ->label('Pwd Change')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedExclamationCircle)
                    ->falseIcon(Heroicon::OutlinedCheckCircle)
                    ->trueColor('warning')
                    ->falseColor('success'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->label('Role'),

                SelectFilter::make('school_id')
                    ->label('School')
                    ->options(School::orderBy('name_en')->pluck('name_en', 'id'))
                    ->searchable(),

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('must_change_password')
                    ->label('Password Change Required'),
            ])
            ->actions([
                EditAction::make(),

                Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon(Heroicon::OutlinedKey)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password')
                    ->modalDescription('This will reset the password to the username and force a password change on next login.')
                    ->action(function (User $record) {
                        $record->update([
                            'password'             => Hash::make($record->username),
                            'must_change_password' => true,
                        ]);
                        Notification::make()
                            ->title('Password reset to username')
                            ->success()
                            ->send();
                    }),

                Action::make('toggle_active')
                    ->label(fn(User $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn(User $record) => $record->is_active ? Heroicon::OutlinedXCircle : Heroicon::OutlinedCheckCircle)
                    ->color(fn(User $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update(['is_active' => !$record->is_active]);
                        Notification::make()
                            ->title('User ' . ($record->is_active ? 'activated' : 'deactivated'))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('generate_passwords')
                        ->label('Generate Passwords')
                        ->icon(Heroicon::OutlinedKey)
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Generate Passwords')
                        ->modalDescription('Random passwords will be generated for selected users. Save or print the list shown.')
                        ->action(function (Collection $records) {
                            $results = [];
                            foreach ($records as $user) {
                                $password = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789@#'), 0, 8);
                                $user->update([
                                    'password'             => Hash::make($password),
                                    'must_change_password' => true,
                                    'is_active'            => true,
                                ]);
                                $results[] = $user->name . ' (' . $user->username . ') — ' . $password;
                            }
                            Notification::make()
                                ->title('Passwords Generated (' . count($results) . ' users)')
                                ->body(implode("\n", $results))
                                ->success()
                                ->persistent()
                                ->send();
                        }),

                    BulkAction::make('activate_users')
                        ->label('Activate Selected')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(fn($user) => $user->update(['is_active' => true]));
                            Notification::make()
                                ->title($records->count() . ' users activated')
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('deactivate_users')
                        ->label('Deactivate Selected')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(fn($user) => $user->update(['is_active' => false]));
                            Notification::make()
                                ->title($records->count() . ' users deactivated')
                                ->warning()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}
