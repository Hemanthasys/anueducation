<?php

namespace App\Enums;

enum TeacherStatus: string
{
    case Active            = 'active';
    case Attached          = 'attached';
    case MaternityLeave    = 'maternity_leave';
    case MedicalLeave      = 'medical_leave';
    case OtherLeave        = 'other_leave';
    case TransferredOut    = 'transferred_out';
    case TransferredIn     = 'transferred_in';
    case PromotedPrincipal = 'promoted_principal';
    case Retired           = 'retired';
    case Deceased          = 'deceased';
    case Abroad            = 'abroad';
    case Resigned          = 'resigned';

    public function label(): string
    {
        return match($this) {
            self::Active            => 'Active',
            self::Attached          => 'Attached to Another School',
            self::MaternityLeave    => 'Maternity Leave',
            self::MedicalLeave      => 'Medical Leave',
            self::OtherLeave        => 'Other Leave',
            self::TransferredOut    => 'Transferred Out of Zone',
            self::TransferredIn     => 'Transferred Into Zone',
            self::PromotedPrincipal => 'Promoted as Principal',
            self::Retired           => 'Retired',
            self::Deceased          => 'Deceased',
            self::Abroad            => 'Gone Abroad',
            self::Resigned          => 'Resigned',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Active, self::Attached]);
    }

    public function color(): string
    {
        return match($this) {
            self::Active            => 'success',
            self::Attached          => 'info',
            self::MaternityLeave    => 'warning',
            self::MedicalLeave      => 'warning',
            self::OtherLeave        => 'warning',
            self::TransferredOut    => 'gray',
            self::TransferredIn     => 'info',
            self::PromotedPrincipal => 'success',
            self::Retired           => 'gray',
            self::Deceased          => 'danger',
            self::Abroad            => 'gray',
            self::Resigned          => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function activeStatuses(): array
    {
        return collect(self::cases())
            ->filter(fn ($case) => $case->isActive())
            ->map(fn ($case) => $case->value)
            ->toArray();
    }

    public static function inactiveStatuses(): array
    {
        return collect(self::cases())
            ->filter(fn ($case) => !$case->isActive())
            ->map(fn ($case) => $case->value)
            ->toArray();
    }
}