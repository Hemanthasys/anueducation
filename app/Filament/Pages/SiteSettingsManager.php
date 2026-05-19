<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
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

class SiteSettingsManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected string $view                                  = 'filament.pages.site-settings-manager';

    // Single state array
    public array $data = [];

    public static function getNavigationLabel(): string { return 'Site Settings'; }
    public function getTitle(): string                  { return 'Site Settings'; }
    public static function getNavigationGroup(): string { return 'Settings'; }
    public static function getNavigationSort(): ?int    { return 3; }
    public static function canAccess(): bool            { return Auth::user()?->hasRole('super_admin') ?? false; }

    public function mount(): void
    {
        $this->form->fill([
            // General
            'site_name_en'        => SiteSetting::get('site_name_en', 'Zonal Education Office'),
            'site_name_si'        => SiteSetting::get('site_name_si', 'කලාප අධ්‍යාපන කාර්යාලය'),
            'site_tagline_en'     => SiteSetting::get('site_tagline_en', 'Anuradhapura'),
            'site_tagline_si'     => SiteSetting::get('site_tagline_si', 'අනුරාධපුර'),
            'title_separator'     => SiteSetting::get('title_separator', '|'),

            // Contact
            'phone'               => SiteSetting::get('phone', ''),
            'email'               => SiteSetting::get('email', ''),
            'address_en'          => SiteSetting::get('address_en', ''),
            'address_si'          => SiteSetting::get('address_si', ''),
            'whatsapp_no'         => SiteSetting::get('whatsapp_no', ''),

            // Social
            'facebook_url'        => SiteSetting::get('facebook_url', ''),
            'youtube_url'         => SiteSetting::get('youtube_url', ''),

            // SEO
            'meta_description_en' => SiteSetting::get('meta_description_en', ''),
            'meta_description_si' => SiteSetting::get('meta_description_si', ''),
            'meta_keywords'       => SiteSetting::get('meta_keywords', ''),
            'google_analytics_id' => SiteSetting::get('google_analytics_id', ''),

            // Favicon
            'favicon'             => SiteSetting::get('favicon') ? [SiteSetting::get('favicon')] : [],

            // Footer
            'footer_text_en'      => SiteSetting::get('footer_text_en', ''),
            'footer_text_si'      => SiteSetting::get('footer_text_si', ''),
            'copyright_en'        => SiteSetting::get('copyright_en', ''),
            'copyright_si'        => SiteSetting::get('copyright_si', ''),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Tabs::make('SiteSettingsTabs')
                    ->tabs([

                        // ── GENERAL ───────────────────────────────────────
                        Tab::make('General')
                            ->schema([
                                Section::make('Site Name')
                                    ->description('Displayed in the browser tab title and throughout the site.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('site_name_en')
                                            ->label('Site Name (English)')
                                            ->required()
                                            ->maxLength(150),

                                        TextInput::make('site_name_si')
                                            ->label('Site Name (Sinhala) / වෙබ් අඩවි නම')
                                            ->required()
                                            ->maxLength(150),

                                        TextInput::make('site_tagline_en')
                                            ->label('Tagline (English)')
                                            ->placeholder('e.g. Anuradhapura')
                                            ->maxLength(150),

                                        TextInput::make('site_tagline_si')
                                            ->label('Tagline (Sinhala) / උප නම')
                                            ->placeholder('e.g. අනුරාධපුර')
                                            ->maxLength(150),
                                    ]),

                                Section::make('Page Title Format')
                                    ->description('Controls how each page title appears in the browser tab. Format: Page Name [separator] Site Name [separator] Tagline')
                                    ->schema([
                                        TextInput::make('title_separator')
                                            ->label('Title Separator')
                                            ->placeholder('|')
                                            ->maxLength(5)
                                            ->helperText('Character between page name and site name. Usually | or —'),
                                    ]),
                            ]),

                        // ── CONTACT ───────────────────────────────────────
                        Tab::make('Contact')
                            ->schema([
                                Section::make('Contact Details')
                                    ->description('Displayed in the topbar, footer, and contact page.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('phone')
                                            ->label('Phone Number')
                                            ->placeholder('e.g. 025-2222000')
                                            ->tel()
                                            ->maxLength(20),

                                        TextInput::make('email')
                                            ->label('Email Address')
                                            ->placeholder('e.g. info@anueducation.lk')
                                            ->email()
                                            ->maxLength(150),

                                        TextInput::make('whatsapp_no')
                                            ->label('WhatsApp Number (with country code)')
                                            ->placeholder('e.g. +94771234567')
                                            ->maxLength(20),
                                    ]),

                                Section::make('Office Address')
                                    ->columns(2)
                                    ->schema([
                                        Textarea::make('address_en')
                                            ->label('Address (English)')
                                            ->rows(3)
                                            ->maxLength(300),

                                        Textarea::make('address_si')
                                            ->label('Address (Sinhala) / ලිපිනය')
                                            ->rows(3)
                                            ->maxLength(300),
                                    ]),
                            ]),

                        // ── SOCIAL MEDIA ──────────────────────────────────
                        Tab::make('Social Media')
                            ->schema([
                                Section::make('Social Media Links')
                                    ->description('Links displayed in the topbar and footer.')
                                    ->schema([
                                        TextInput::make('facebook_url')
                                            ->label('Facebook Page URL')
                                            ->placeholder('https://facebook.com/...')
                                            ->url()
                                            ->maxLength(255),

                                        TextInput::make('youtube_url')
                                            ->label('YouTube Channel URL')
                                            ->placeholder('https://youtube.com/...')
                                            ->url()
                                            ->maxLength(255),
                                    ]),
                            ]),

                        // ── SEO & META ────────────────────────────────────
                        Tab::make('SEO & Meta')
                            ->schema([
                                Section::make('Meta Description')
                                    ->description('Shown in Google search results. Keep under 160 characters.')
                                    ->columns(2)
                                    ->schema([
                                        Textarea::make('meta_description_en')
                                            ->label('Meta Description (English)')
                                            ->rows(3)
                                            ->maxLength(160),

                                        Textarea::make('meta_description_si')
                                            ->label('Meta Description (Sinhala)')
                                            ->rows(3)
                                            ->maxLength(160),
                                    ]),

                                Section::make('Keywords & Analytics')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('meta_keywords')
                                            ->label('Meta Keywords')
                                            ->placeholder('education, anuradhapura, schools...')
                                            ->helperText('Comma separated. Less important for modern SEO but still useful.')
                                            ->maxLength(255),

                                        TextInput::make('google_analytics_id')
                                            ->label('Google Analytics ID')
                                            ->placeholder('G-XXXXXXXXXX')
                                            ->helperText('Leave empty if not using Google Analytics.')
                                            ->maxLength(50),
                                    ]),
                            ]),

                        // ── FAVICON ───────────────────────────────────────
                        Tab::make('Favicon')
                            ->schema([
                                Section::make('Site Favicon')
                                    ->description('The small icon shown in browser tabs and bookmarks. Recommended: 32 × 32 px or 64 × 64 px square. Accepted formats: PNG, ICO, JPG. Max size: 512 KB. Use a square image for best results across all browsers.')
                                    ->schema([
                                        FileUpload::make('favicon')
                                            ->label('Favicon Image')
                                            ->image()
                                            ->disk('public')
                                            ->directory('favicon')
                                            ->maxSize(512)
                                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/jpeg', 'image/webp'])
                                            ->imagePreviewHeight('64')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ── FOOTER ────────────────────────────────────────
                        Tab::make('Footer')
                            ->schema([
                                Section::make('Footer Text')
                                    ->description('Short tagline shown in the footer above copyright.')
                                    ->columns(2)
                                    ->schema([
                                        Textarea::make('footer_text_en')
                                            ->label('Footer Text (English)')
                                            ->rows(3)
                                            ->maxLength(300),

                                        Textarea::make('footer_text_si')
                                            ->label('Footer Text (Sinhala) / පාදක පාඨය')
                                            ->rows(3)
                                            ->maxLength(300),
                                    ]),

                                Section::make('Copyright')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('copyright_en')
                                            ->label('Copyright Line (English)')
                                            ->placeholder('e.g. Zonal Education Office, Anuradhapura. All rights reserved.')
                                            ->maxLength(255),

                                        TextInput::make('copyright_si')
                                            ->label('Copyright Line (Sinhala) / කතුහිමිකම')
                                            ->maxLength(255),
                                    ]),
                            ]),

                    ])
                    ->columnSpanFull(),

            ]);
    }

    // Save General tab
    public function saveGeneral(): void
    {
        $data = $this->data;
        $keys = ['site_name_en', 'site_name_si', 'site_tagline_en', 'site_tagline_si', 'title_separator'];
        foreach ($keys as $key) {
            SiteSetting::set($key, $data[$key] ?? '');
            Cache::forget("site_setting_{$key}");
        }
        Notification::make()->title('General settings saved.')->success()->send();
    }

    // Save Contact tab
    public function saveContact(): void
    {
        $data = $this->data;
        $keys = ['phone', 'email', 'address_en', 'address_si', 'whatsapp_no'];
        foreach ($keys as $key) {
            SiteSetting::set($key, $data[$key] ?? '');
            Cache::forget("site_setting_{$key}");
        }
        Notification::make()->title('Contact settings saved.')->success()->send();
    }

    // Save Social Media tab
    public function saveSocial(): void
    {
        $data = $this->data;
        $keys = ['facebook_url', 'youtube_url'];
        foreach ($keys as $key) {
            SiteSetting::set($key, $data[$key] ?? '');
            Cache::forget("site_setting_{$key}");
        }
        Notification::make()->title('Social media settings saved.')->success()->send();
    }

    // Save SEO tab
    public function saveSeo(): void
    {
        $data = $this->data;
        $keys = ['meta_description_en', 'meta_description_si', 'meta_keywords', 'google_analytics_id'];
        foreach ($keys as $key) {
            SiteSetting::set($key, $data[$key] ?? '');
            Cache::forget("site_setting_{$key}");
        }
        Notification::make()->title('SEO settings saved.')->success()->send();
    }

    // Save Favicon tab
 public function saveFavicon(): void
{
    $state   = $this->form->getState();
    $favicon = $state['favicon'] ?? [];
    if (!empty($favicon)) {
        $path = is_array($favicon) ? array_values($favicon)[0] : $favicon;
        SiteSetting::set('favicon', $path);
        Cache::forget('site_setting_favicon');
    }
    Notification::make()->title('Favicon saved.')->success()->send();
}

    // Save Footer tab
    public function saveFooter(): void
    {
        $data = $this->data;
        $keys = ['footer_text_en', 'footer_text_si', 'copyright_en', 'copyright_si'];
        foreach ($keys as $key) {
            SiteSetting::set($key, $data[$key] ?? '');
            Cache::forget("site_setting_{$key}");
        }
        Notification::make()->title('Footer settings saved.')->success()->send();
    }
}
