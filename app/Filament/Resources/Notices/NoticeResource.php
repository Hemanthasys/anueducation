<?php

namespace App\Filament\Resources\Notices;

use App\Filament\Resources\Notices\Pages\CreateNotice;
use App\Filament\Resources\Notices\Pages\EditNotice;
use App\Filament\Resources\Notices\Pages\ListNotices;
use App\Filament\Resources\Notices\Schemas\NoticeForm;
use App\Filament\Resources\Notices\Tables\NoticesTable;
use App\Models\Notice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Traits\HasViewManagePermissions;

class NoticeResource extends Resource
{
    use HasViewManagePermissions;
    protected static string $viewPermission   = 'content.notices';
    protected static string $managePermission = 'content.notices';

    protected static ?string $model = Notice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title_en';

    public static function getNavigationGroup(): string
    {
        return 'Website Content';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }



    public static function form(Schema $schema): Schema
    {
        return NoticeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NoticesTable::configure($table);
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
            'index' => ListNotices::route('/'),
            'create' => CreateNotice::route('/create'),
            'edit' => EditNotice::route('/{record}/edit'),
        ];
    }
}
