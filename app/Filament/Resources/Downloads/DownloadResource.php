<?php

namespace App\Filament\Resources\Downloads;

use App\Filament\Resources\Downloads\Pages\CreateDownload;
use App\Filament\Resources\Downloads\Pages\EditDownload;
use App\Filament\Resources\Downloads\Pages\ListDownloads;
use App\Filament\Resources\Downloads\Schemas\DownloadForm;
use App\Filament\Resources\Downloads\Tables\DownloadsTable;
use App\Models\Download;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Traits\HasViewManagePermissions;

class DownloadResource extends Resource
{
    
    use HasViewManagePermissions;
    protected static string $viewPermission   = 'content.downloads';
    protected static string $managePermission = 'content.downloads';
    protected static ?string $model = Download::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title_en';

    public static function getNavigationGroup(): string
    {
        return 'Website Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }


    public static function form(Schema $schema): Schema
    {
        return DownloadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DownloadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDownloads::route('/'),
            'create' => CreateDownload::route('/create'),
            'edit' => EditDownload::route('/{record}/edit'),
        ];
    }
}
