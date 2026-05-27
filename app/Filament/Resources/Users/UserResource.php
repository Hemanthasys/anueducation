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
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
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
                        ->relationship('roles', 'name')
                        ->preload()
                        ->required()
                        ->searchable(),

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

                    Select::make('subject_id')
                        ->label('Appointed Subject')
                        ->options(Subject::active()->pluck('name_en', 'id'))
                        ->searchable()
                        ->nullable()
                        ->helperText('For teacher role'),
                ]),

            Section::make('Employment')
                ->columns(2)
                ->schema([
                    DatePicker::make('appointed_date')
                        ->label('First Appointed Date')
                        ->maxDate(today()),

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

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('must_change_password')
                    ->label('Password Change Required'),
            ])
            ->actions([
                EditAction::make(),

                // Reset password to username
                Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon(Heroicon::OutlinedKey)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password')
                    ->modalDescription('This will reset the password to the username and force a password change on next login.')
                    ->action(function (User $record) {
                        $record->resetToDefaultPassword();
                        Notification::make()
                            ->title('Password reset to username')
                            ->success()
                            ->send();
                    }),

                // Toggle active/inactive
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