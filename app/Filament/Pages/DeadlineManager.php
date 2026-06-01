<?php

namespace App\Filament\Pages;

use App\Models\StatDeadline;
use App\Models\StatSnapshot;
use App\Services\StatisticsService;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class DeadlineManager extends Page
{
    protected string $view = 'filament.pages.deadline-manager';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'Deadline Manager';

    public static function getNavigationGroup(): string
    {
        return 'Statistics';
    }

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()->can('statistics.manage_deadlines') || auth()->user()->hasRole('super_admin');
    }

    public function getDeadlines()
    {
        return StatDeadline::orderBy('created_at', 'desc')->get();
    }

    public function getLatestSnapshot()
    {
        return StatSnapshot::latest()->first();
    }

    public function getComplianceReport(): array
    {
        return app(StatisticsService::class)->getComplianceReport();
    }

    protected function getHeaderActions(): array
    {
        return [

            // Set new deadline
            Action::make('set_deadline')
                ->label('Set New Deadline')
                ->icon(Heroicon::OutlinedCalendarDays)
                ->color('primary')
                ->form([
                    TextInput::make('academic_year')
                        ->label('Academic Year')
                        ->placeholder('2025/2026')
                        ->required(),
                    DateTimePicker::make('deadline_date')
                        ->label('Deadline Date & Time')
                        ->required()
                        ->minDate(now()),
                ])
                ->action(function (array $data) {
                    StatDeadline::where('is_active', true)->update(['is_active' => false]);

                    $deadline = StatDeadline::create([
                        'academic_year' => $data['academic_year'],
                        'deadline_date' => $data['deadline_date'],
                        'is_active'     => true,
                    ]);

                    app(StatisticsService::class)->createComplianceRecords($deadline);

                    Notification::make()
                        ->title('Deadline set for ' . $data['academic_year'])
                        ->success()
                        ->send();
                }),

            // Manual snapshot trigger
            Action::make('trigger_snapshot')
                ->label('Generate Snapshot Now')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription('This will generate a statistics snapshot from current submitted data and lock all submissions. Are you sure?')
                ->action(function () {
                    $service  = app(StatisticsService::class);
                    $deadline = StatDeadline::where('is_active', true)->first();

                    $academicYear = $deadline?->academic_year ?? $service->getCurrentAcademicYear();
                    $snapshot     = $service->generateSnapshot($academicYear, $deadline);

                    // Mark pending schools as overdue
                    if ($deadline) {
                        $service->markOverdueSchools($deadline);
                        $deadline->update(['triggered_at' => now()]);
                    }

                    Notification::make()
                        ->title('Snapshot generated for ' . $academicYear)
                        ->success()
                        ->send();
                }),

        ];
    }
}