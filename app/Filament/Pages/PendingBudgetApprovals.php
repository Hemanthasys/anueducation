<?php

namespace App\Filament\Pages;

use App\Models\SchoolBudgetApproval;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Filament\Support\Icons\Heroicon;

class PendingBudgetApprovals extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getNavigationGroup(): string
    {
        return 'Planning & Development';
    }

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.pending-budget-approvals';

    public static function getNavigationLabel(): string
    {
        return __('pending_budget_approvals');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::pendingQuery()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    // ─── Access control ───────────────────────────────────────────────────────

    public static function canAccess(): bool
    {
        return auth()->user()?->can('budget.approve') || auth()->user()?->hasRole('super_admin') ?? false;
    }

    // ─── Query ────────────────────────────────────────────────────────────────

    private static function pendingQuery(): Builder
    {
        return SchoolBudgetApproval::with(['school.division', 'submittedBy'])
            ->where('status', 'submitted')
            ->latest('submitted_at');
    }

    public function getPendingApprovals(): Collection
    {
        return static::pendingQuery()->get()->map(function (SchoolBudgetApproval $approval) {
            $approval->income_total = \App\Models\SchoolBudgetIncome::where('school_id', $approval->school_id)
                ->where('academic_year', $approval->academic_year)
                ->sum('expected_amount');

            $approval->expenditure_total = \App\Models\SchoolBudgetExpenditure::where('school_id', $approval->school_id)
                ->where('academic_year', $approval->academic_year)
                ->sum('expected_amount');

            return $approval;
        });
    }

    // ─── Actions ─────────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function approveAction(): Action
    {
        return Action::make('approve')
            ->label(__('approve'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__('approve_budget'))
            ->modalDescription(__('approve_budget_desc'))
            ->modalSubmitActionLabel(__('confirm_approve'))
            ->action(function (array $arguments): void {
                $approval = SchoolBudgetApproval::findOrFail($arguments['approval_id']);

                $approval->approve(auth()->id());

                Notification::make()
                    ->title(__('budget_approved_success'))
                    ->success()
                    ->send();
            });
    }

    public function rejectAction(): Action
    {
        return Action::make('reject')
            ->label(__('reject'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->form([
                Textarea::make('rejection_reason')
                    ->label(__('rejection_reason'))
                    ->placeholder(__('rejection_reason_placeholder'))
                    ->required()
                    ->minLength(10)
                    ->rows(3)
                    ->maxLength(1000),
            ])
            ->modalHeading(__('reject_budget'))
            ->modalDescription(__('reject_budget_desc'))
            ->modalSubmitActionLabel(__('confirm_reject'))
            ->action(function (array $arguments, array $data): void {
                $approval = SchoolBudgetApproval::findOrFail($arguments['approval_id']);

                $approval->reject(auth()->id(), $data['rejection_reason']);

                Notification::make()
                    ->title(__('budget_rejected_success'))
                    ->warning()
                    ->send();
            });
    }
}
