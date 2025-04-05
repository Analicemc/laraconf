<?php 

namespace App\Enums;

Enum TalkStatus: string{
    case SUBMITTED =  'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function getColor(): string
    {
        return match ($this) {
            self::SUBMITTED => 'primary',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}