<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\User;
use App\Models\Queue;
use App\Models\Doctor;
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
            $doctor = Doctor::get()->count();
            $user = User::get()->count();
            $appointment = Appointment::get()->count();
            $queue = Queue::onlyTrashed()->get()->count();
            $response = [
                'data' => [
                    'doctor_count' => $doctor,
                    'user_count' => $user,
                    'queue_count' => $queue,
                    'appointment_count' => $appointment,
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
