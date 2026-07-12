<?php

namespace App\Filament\Pages;

use App\Exports\TeacherTemplateExport;
use App\Imports\TeacherImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;

class TeacherBulkUpload extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected string $view = 'filament.pages.teacher-bulk-upload';

    public static function getNavigationLabel(): string
    {
        return __('Teacher Bulk Upload');
    }

    public static function getNavigationGroup(): string
    {
        return 'Administration';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('teachers.manage') || auth()->user()?->hasRole('super_admin') ?? false;
    }

    // ── Upload state ──────────────────────────────────────────────────────────

    public ?array $results = null;
    public bool $uploaded  = false;

    // ── Header actions ────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label(__('Download Template'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(function () {
                    return Excel::download(
                        new TeacherTemplateExport(),
                        'teacher-upload-template-' . now()->format('Y-m-d') . '.xlsx'
                    );
                }),
        ];
    }

    // ── Upload action ─────────────────────────────────────────────────────────

    public function uploadAction(): Action
    {
        return Action::make('upload')
            ->label(__('Upload File'))
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->color('primary')
            ->form([
                FileUpload::make('file')
                    ->label(__('Select Excel File'))
                    ->disk('local')
                    ->directory('teacher-imports')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                    ])
                    ->maxSize(10240) // 10MB
                    ->required()
                    ->helperText(__('Upload the completed teacher template (.xlsx). Max 10MB.')),
            ])
            ->modalHeading(__('Upload Teacher Data'))
            ->modalDescription(__('Please ensure you have used the official template. Invalid rows will be skipped with a reason.'))
            ->modalSubmitActionLabel(__('Upload & Import'))
            ->action(function (array $data): void {
                $path = storage_path('app/private/teacher-imports/' . basename($data['file']));

                $import = new TeacherImport();

                try {
                    Excel::import($import, $path);

                    $this->results  = $import->results;
                    $this->uploaded = true;

                    Notification::make()
                        ->title(__('Import Complete'))
                        ->body(
                            __('Created: ') . $import->results['created'] .
                            ' | ' . __('Skipped: ') . $import->results['skipped']
                        )
                        ->success()
                        ->send();

                } catch (\Exception $e) {
                    Notification::make()
                        ->title(__('Import Failed'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    public function resetAction(): Action
    {
        return Action::make('reset')
            ->label(__('Upload Another File'))
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('gray')
            ->action(function (): void {
                $this->results  = null;
                $this->uploaded = false;
            });
    }
}