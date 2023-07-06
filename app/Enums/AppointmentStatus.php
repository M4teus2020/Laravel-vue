<?php 

namespace App\Enums;

enum AppointmentStatus: int 
{
    case SCHEDULED = 1;
    case CONFIRMED = 2;
    case CANCELLED = 3;

    public function color() {
        return match($this) {
            self::SCHEDULED => 'primary',
            self::CONFIRMED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}

?>