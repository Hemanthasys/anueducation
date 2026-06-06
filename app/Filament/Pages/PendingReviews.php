<?php

namespace App\Filament\Pages;

use App\Models\MilestoneUpdate;
use App\Models\ProjectAssignment;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Filament\Support\Icons\Heroicon;

class PendingReviews extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function getNavigationGroup(): string
    {
        return 'Projects';
    }

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.pending-reviews';

    public static function getNavigationLabel(): string
    {
        return __('pending_reviews');
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
        $user = auth()->user();

        if ($user->hasRole(['super_admin', 'zonal_director'])) {
            return true;
        }

        // Show page to anyone assigned as overseer with pending updates
        return ProjectAssignment::where('assigned_to', $user->id)
            ->whereHas('milestoneUpdates', fn ($q) => $q->where('status', 'pending'))
            ->exists();
    }

    // ─── Query ────────────────────────────────────────────────────────────────

    private static function pendingQuery(): Builder
    {
        $user = auth()->user();

        $query = MilestoneUpdate::with([
            'milestone',
            'assignment.project.fundingSource',  // assignment() not projectAssignment()
            'assignment.school.division',
            'submittedBy',
            'photos',
        ])->where('status', 'pending');

        if (! $user->hasRole(['super_admin', 'zonal_director'])) {
            $query->whereHas('assignment', fn ($q) =>
                $q->where('assigned_to', $user->id)
            );
        }

        return $query->latest();
    }

    public function getPendingUpdates(): Collection
    {
        return static::pendingQuery()->get();
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
            ->form([
                Textarea::make('review_note')
                    ->label(__('approval_note'))
                    ->placeholder(__('approval_note_placeholder'))
                    ->rows(3)
                    ->maxLength(1000),
            ])
            ->modalHeading(__('approve_update'))
            ->modalDescription(__('approve_update_desc'))
            ->modalSubmitActionLabel(__('confirm_approve'))
            ->action(function (array $arguments, array $data): void {
                $update = MilestoneUpdate::findOrFail($arguments['update_id']);

                abort_unless($this->canReview($update), 403);

                $update->approve(auth()->id(), $data['review_note'] ?? null);

                Notification::make()
                    ->title(__('update_approved_success'))
                    ->success()
                    ->send();
            });
    }

    public function rejectAction(): Action
    {
        return Action::make('reject')
            ->label(__('reject'))
            ->icon('heroicon-o-x-circle')
                ->form([
                Textarea::make('review_note')
                    ->label(__('rejection_reason'))
                    ->placeholder(__('rejection_reason_placeholder'))
                    ->required()
                    ->minLength(10)
                    ->rows(3)
                    ->maxLength(1000),
            ])
            ->modalHeading(__('reject_update'))
            ->modalDescription(__('reject_update_desc'))
            ->modalSubmitActionLabel(__('confirm_reject'))
           
            ->action(function (array $arguments, array $data): void {
                $update = MilestoneUpdate::findOrFail($arguments['update_id']);

                abort_unless($this->canReview($update), 403);

                $update->reject(auth()->id(), $data['review_note']);

                Notification::make()
                    ->title(__('update_rejected_success'))
                    ->warning()
                    ->send();
            });
    }

    private function canReview(MilestoneUpdate $update): bool
    {
        $user = auth()->user();

        if ($user->hasRole(['super_admin', 'zonal_director'])) {
            return true;
        }

        // assignment() is the correct relationship name
        return $update->assignment?->assigned_to === $user->id;
    }
}