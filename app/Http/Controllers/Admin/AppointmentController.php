<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::query()
            ->with('client:id,first_name,last_name')
            ->paginate()
            ->through(fn ($appointments) => [
                'id' => $appointments->id,
                'start_time' => $appointments->start_time->format('Y-m-d h:i A'),
                'end_time' => $appointments->end_time->format('Y-m-d h:i A'),
                'client' => $appointments->client,
                'status' => [
                    'name' => $appointments->status->name,
                    'color' => $appointments->status->color(),
                ],
            ]);

        return $appointments;
    }
}
