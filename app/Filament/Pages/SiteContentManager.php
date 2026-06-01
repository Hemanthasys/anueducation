<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SiteContentManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;
    protected string $view                                  = 'filament.pages.site-content-manager';

    // One array for all form data — matches Filament page pattern
    public array $data = [];

    public static function getNavigationLabel(): string { return 'Site Content'; }
    public function getTitle(): string                  { return 'Site Content Manager'; }
    public static function getNavigationGroup(): string { return 'Settings'; }
    public static function getNavigationSort(): ?int    { return 2; }
    public static function canAccess(): bool            { return Auth::user()?->can('settings.content') || Auth::user()?->hasRole('super_admin') ?? false; }
    
    public function mount(): void
    {
        $existing = SiteSetting::get('director_photo');

        $this->form->fill([
            'director_name_en'        => SiteSetting::get('director_name_en', ''),
            'director_name_si'        => SiteSetting::get('director_name_si', ''),
            'director_designation_en' => SiteSetting::get('director_designation_en', 'Zonal Director of Education'),
            'director_designation_si' => SiteSetting::get('director_designation_si', 'කලාප අධ්‍යාපන අධ්‍යක්ෂ'),
            'director_photo'          => $existing ? [$existing] : [],
            'director_phone'          => SiteSetting::get('director_phone', ''),
            'director_email'          => SiteSetting::get('director_email', ''),
            'director_facebook'       => SiteSetting::get('director_facebook', ''),
            'director_whatsapp'       => SiteSetting::get('director_whatsapp', ''),
            'director_message_en'     => SiteSetting::get('director_message_en', ''),
            'director_message_si'     => SiteSetting::get('director_message_si', ''),
            'vision_en'               => SiteSetting::get('vision_en', ''),
            'vision_si'               => SiteSetting::get('vision_si', ''),
            'mission_en'              => SiteSetting::get('mission_en', ''),
            'mission_si'              => SiteSetting::get('mission_si', ''),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Tabs::make('SiteContentTabs')
                    ->tabs([

                        // ── DIRECTOR TAB ─────────────────────────────────
                        Tab::make('Zonal Director')
                            ->schema([

                                Section::make('Director Identity')
                                    ->description('Name and designation displayed on the homepage.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('director_name_en')
                                            ->label('Full Name (English)')
                                            ->placeholder('e.g. Mr. K. A. Perera')
                                            ->maxLength(150),

                                        TextInput::make('director_name_si')
                                            ->label('Full Name (Sinhala) / සම්පූර්ණ නම')
                                            ->placeholder('e.g. ශ්‍රී. ක. අ. පෙරේරා')
                                            ->maxLength(150),

                                        TextInput::make('director_designation_en')
                                            ->label('Designation (English)')
                                            ->maxLength(200),

                                        TextInput::make('director_designation_si')
                                            ->label('Designation (Sinhala) / තනතුර')
                                            ->maxLength(200),
                                    ]),

                                Section::make('Director Photo')
                                    ->description('Recommended: 400 × 500 px | JPG or PNG | Max 2 MB | Appears full-height on the homepage.')
                                    ->schema([
                                        FileUpload::make('director_photo')
                                            ->label('Portrait Photo')
                                            ->image()
                                            ->disk('public')
                                            ->directory('director')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                                            ->imagePreviewHeight('200')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Contact & Social Media')
                                    ->description('Contact details shown under the Director\'s photo on the homepage.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('director_phone')
                                            ->label('Phone Number')
                                            ->placeholder('e.g. +94 25 222 2000')
                                            ->tel()
                                            ->maxLength(20),

                                        TextInput::make('director_email')
                                            ->label('Email Address')
                                            ->placeholder('e.g. director@anueducation.lk')
                                            ->email()
                                            ->maxLength(150),

                                        TextInput::make('director_facebook')
                                            ->label('Facebook Profile URL')
                                            ->placeholder('https://facebook.com/...')
                                            ->url()
                                            ->maxLength(255),

                                        TextInput::make('director_whatsapp')
                                            ->label('WhatsApp Number (with country code)')
                                            ->placeholder('e.g. +94771234567')
                                            ->maxLength(20),
                                    ]),

                                Section::make("Director's Message")
                                    ->description('Shown on the homepage in blockquote style. Supports bold, italic, and lists.')
                                    ->schema([
                                        RichEditor::make('director_message_en')
                                            ->label('Message (English)')
                                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])
                                            ->columnSpanFull(),

                                        RichEditor::make('director_message_si')
                                            ->label('Message (Sinhala) / පණිවිඩය')
                                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])
                                            ->columnSpanFull(),
                                    ]),

                            ]),

                        // ── VISION & MISSION TAB ──────────────────────────
                        Tab::make('Vision & Mission')
                            ->schema([

                                Section::make('Vision Statement / දැක්ම')
                                    ->description('The long-term vision of the Zonal Education Office.')
                                    ->schema([
                                        RichEditor::make('vision_en')
                                            ->label('Vision (English)')
                                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])
                                            ->columnSpanFull(),

                                        RichEditor::make('vision_si')
                                            ->label('Vision (Sinhala) / දැක්ම')
                                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Mission Statement / මෙහෙවර')
                                    ->description('The core mission and purpose of the Zonal Education Office.')
                                    ->schema([
                                        RichEditor::make('mission_en')
                                            ->label('Mission (English)')
                                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])
                                            ->columnSpanFull(),

                                        RichEditor::make('mission_si')
                                            ->label('Mission (Sinhala) / මෙහෙවර')
                                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])
                                            ->columnSpanFull(),
                                    ]),

                            ]),

                    ])
                    ->columnSpanFull(),

            ]);
    }

    public function saveDirector(): void
    {
        // getState() triggers Filament's file processing — moves temp file to disk
        $state = $this->form->getState();

        // Save all text + rich text fields
        $textKeys = [
            'director_name_en', 'director_name_si',
            'director_designation_en', 'director_designation_si',
            'director_phone', 'director_email',
            'director_facebook', 'director_whatsapp',
            'director_message_en', 'director_message_si',
        ];

        foreach ($textKeys as $key) {
            $value = isset($state[$key]) && is_array($state[$key])
                ? json_encode($state[$key])
                : ($state[$key] ?? '');
            SiteSetting::set($key, $value);
            Cache::forget("site_setting_{$key}");
        }

        // getState() returns the processed file path after moving to disk
            $state = $this->form->getState();
            $photo = $state['director_photo'] ?? [];
            if (!empty($photo)) {
                $path = is_array($photo) ? array_values($photo)[0] : $photo;
                SiteSetting::set('director_photo', $path);
                Cache::forget('site_setting_director_photo');
            }

        Notification::make()
            ->title('Director information saved successfully.')
            ->success()
            ->send();
    }

    public function saveVisionMission(): void
    {
        // getState() processes all field values properly
        $state = $this->form->getState();

        foreach (['vision_en', 'vision_si', 'mission_en', 'mission_si'] as $key) {
            $value = isset($state[$key]) && is_array($state[$key])
                ? json_encode($state[$key])
                : ($state[$key] ?? '');
            SiteSetting::set($key, $value);
            Cache::forget("site_setting_{$key}");
        }

        Notification::make()
            ->title('Vision & Mission saved successfully.')
            ->success()
            ->send();
    }
}