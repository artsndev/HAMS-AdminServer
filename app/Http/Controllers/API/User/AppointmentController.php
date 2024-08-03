<?php

namespace App\Http\Controllers\API\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
                'status' => 'required',
                'appointment_time' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'errors' => $validator->errors(),
                ];
                return response()->json($response, 200);
            }
            $appointment = Appointment::create([
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
            //code...
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