<?php

namespace App\Http\Controllers\API\User;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $appointment = Appointment::with('user','doctor')->where('user_id', Auth::user()->id)->latest()->get();
            if(!$appointment) {
                $response = [
                    'success' => false,
                    'message' => 'Not Found',
                ];
                return response()->json($response, 403);
            }
            $response = [
                'success' => true,
                'data' => $appointment,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            //code...
            $validator = Validator::make($request->all(), [
                'purpose_of_appointment' => 'required',
                'session_of_appointment' => 'required',
                'appointment_time' => 'required|date_format:Y-m-d H:i|unique:appointments,appointment_time',
            ]);
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'errors' => $validator->errors(),
                ];
                return response()->json($response, 200);
            }
            $appointment = Appointment::create([
                'user_id' => Auth::user()->id,
                'doctor_id' => $request->input('doctor_id'),
                'purpose_of_appointment' => $request->input('purpose_of_appointment'),
                'session_of_appointment' => $request->input('session_of_appointment'),
                'status' => $request->input('status'),
                'appointment_time' => $request->input('appointment_time')
            ]);
            $response = [
                'success' => true,
                'data' => [
                    'appointment' => $appointment,
                ],
                'message' => "User Appointment successfully",
            ];
            return response()->json($response, 201);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $appointment = Appointment::with('doctor')->latest()->find($id);
            if (!$appointment) {
                $response = [
                    'success' => false,
                    'message' => 'Appointment Not Found'
                ];
                return response()->json($response, 403);
            }
            $response = [
                'success' => true,
                'message' => 'Appointments Found',
                'data' => $appointment,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
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
