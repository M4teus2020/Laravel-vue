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
        $validated = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'client_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ], [
            'client_id.required' => 'The client name field is required.',
        ]);
        
        Appointment::create([
            'title' => $validated['title'],
            'client_id' => $validated['client_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'description' => $validated['description'],
            'status' => AppointmentStatus::SCHEDULED
        ]);

        return response()->json(['mensage' => 'success']);
    }

    public function edit(Appointment $appointment) {
        return $appointment;
    }

    public function update(Appointment $appointment) {
        $validated = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'client_id' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ], [
            'client_id.required' => 'The client name field is required.',
        ]);

        $appointment->update($validated);

        return response()->json(['mensage' => 'success']);
    }
}
