<?php

namespace App\Notifications;

use App\Models\MilestoneUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MilestoneUpdateReviewed extends Notification
{
    use Queueable;

    public function __construct(
        public MilestoneUpdate $milestoneUpdate,
        public string $status,       // 'approved' or 'rejected'
        public ?string $reviewNote = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        // Use assignment() — matches actual model relationship name
        $assignment = $this->milestoneUpdate->assignment;
        $project    = $assignment?->project;
        $milestone  = $this->milestoneUpdate->milestone;

        return [
            'milestone_update_id' => $this->milestoneUpdate->id,
            'project_id'          => $project?->id,
            'project_name'        => $project?->title,   // Project uses 'title' not 'name'
            'milestone_title'     => $milestone?->title,
            'status'              => $this->status,
            'review_note'         => $this->reviewNote,
            'reviewed_by_name'    => $this->milestoneUpdate->reviewedBy?->name,
        ];
    }
}