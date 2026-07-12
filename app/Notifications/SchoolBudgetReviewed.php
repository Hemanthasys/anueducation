<?php

namespace App\Notifications;

use App\Models\SchoolBudgetApproval;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SchoolBudgetReviewed extends Notification
{
    use Queueable;

    public function __construct(
        public SchoolBudgetApproval $budgetApproval,
        public string $status,       // 'approved' or 'rejected'
        public ?string $reviewNote = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'notification_kind'   => 'school_budget',
            'budget_approval_id'  => $this->budgetApproval->id,
            'academic_year'       => $this->budgetApproval->academic_year,
            'status'              => $this->status,
            'review_note'         => $this->reviewNote,
            'reviewed_by_name'    => $this->budgetApproval->reviewedBy?->name,
        ];
    }
}
