<?php

namespace App\Http\Controllers\API\Doctor;

use App\Models\Queue;
use App\Models\Schedule;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Authorizes the users account in api.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function auth(Request $request)
    {
        return $request->user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $schedule = Schedule::get()->count();
            $appointment = Appointment::get()->count();
            $queue = Queue::get()->count();
            $queued = Queue::onlyTrashed()->get()->count();
            $response = [
                'data' => [
                    'schedule_count' => $schedule,
                    'appointment_count' => $appointment,
                    'queue_count' => $queue,
                    'queued_count' => $queued,
                ]
            ];
            return response()->json($response,200);
        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
            ];
            return response()->json($errors, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
