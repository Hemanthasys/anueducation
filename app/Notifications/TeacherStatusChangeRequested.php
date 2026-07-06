<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TeacherStatusChangeRequested extends Notification
{
    use Queueable;

    public function __construct(
        public string $teacherName,
        public string $requestedStatus,
        public string $requestedBy,
        public int $profileChangeRequestId,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'format'                    => 'filament',
            'title'                     => 'Teacher Status Change Request',
            'body'                      => $this->requestedBy . ' requested status change for ' . $this->teacherName . ' → ' . $this->requestedStatus,
            'icon'                      => 'heroicon-o-user-circle',
            'iconColor'                 => 'warning',
            'profile_change_request_id' => $this->profileChangeRequestId,
            'teacher_name'              => $this->teacherName,
            'requested_status'          => $this->requestedStatus,
            'requested_by'              => $this->requestedBy,
            'type'                      => 'teacher_status_change',
        ];
    }
}