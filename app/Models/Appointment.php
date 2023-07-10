<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'client_id',
        'description',
        'start_time',
        'end_time',
        'status'
    ];

    protected $appends = [
        'formatted_start_time',
        'formatted_end_time'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => AppointmentStatus::class,
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function formattedStartTime(): Attribute {
        return Attribute::make(
            get: fn() => $this->start_time->format('Y-m-d H:i A')
        );
    }

    public function formattedEndTime(): Attribute {
        return Attribute::make(
            get: fn() => $this->end_time->format('Y-m-d H:i A')
        );
    }
}
