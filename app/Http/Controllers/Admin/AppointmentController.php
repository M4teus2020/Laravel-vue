<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::query()
            ->with('client:id,first_name,last_name')
            ->when(request('status'), function ($query) {
                return $query->where('status', AppointmentStatus::from(request('status')));
            })
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

    public function store() {
        Appointment::create([
            'title' => request('title'),
            'client_id' => request('client_id'),
            'start_time' => now(),
            'end_time' => now(),
            'description' => request('description'),
            'status' => AppointmentStatus::SCHEDULED
        ]);

        return response()->json(['mensage' => 'success']);
    }
}
