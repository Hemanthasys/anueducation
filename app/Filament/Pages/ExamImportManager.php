<?php

namespace App\Filament\Pages;

use App\Models\AlExamImport;
use App\Models\AlResult;
use App\Models\OlExamImport;
use App\Models\OlResult;
use App\Models\Grade5ExamImport;
use App\Models\Grade5Result;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ExamImportManager extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;
    protected string $view                                  = 'filament.pages.exam-import-manager';

    public static function getNavigationLabel(): string { return 'Exam Results Import'; }
    public function getTitle(): string                  { return 'Exam Results Import Manager'; }
    public static function getNavigationGroup(): string { return 'Examinations'; }
    public static function getNavigationSort(): ?int    { return 1; }

    public static function canAccess(): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'zonal_director']) ?? false;
    }

    // Delete an import record + its results
    public function deleteImport(string $type, int $id): void
    {
        match ($type) {
            'al' => function () use ($id) {
                $import = AlExamImport::findOrFail($id);
                AlResult::where('import_id', $import->id)->delete();
                $import->delete();
            },
            'ol' => function () use ($id) {
                $import = OlExamImport::findOrFail($id);
                OlResult::where('import_id', $import->id)->delete();
                $import->delete();
            },
            'g5' => function () use ($id) {
                $import = Grade5ExamImport::findOrFail($id);
                $import->results()->delete();
                $import->delete();
            },
            default => null,
        };

        Notification::make()->title('Import deleted successfully.')->success()->send();
    }

    public function getAlImports()   { return AlExamImport::orderByDesc('year')->orderByDesc('created_at')->get(); }
    public function getOlImports()   { return OlExamImport::orderByDesc('year')->orderByDesc('created_at')->get(); }
    public function getG5Imports()   { return Grade5ExamImport::orderByDesc('year')->orderByDesc('imported_at')->get(); }
}
