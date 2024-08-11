<?php

namespace App\Http\Controllers\API\Doctor;

use App\Models\Queue;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Mail\Doctor\QueuingMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class QueuingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $queue = Queue::with([
                'user',
                'appointment' => function ($query) {
                    $query->withTrashed();
                }
            ])->withTrashed()->latest()->get();
            $response = [
                'success' => true,
                'data' => $queue
            ];
            return response()->json($response, 200);
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
        try {
            $appointment = Appointment::findOrFail($request->input('appointment_id'));
            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found.',
                ], 404);
            }
            $queue = Queue::create([
                'doctor_id' => Auth::user()->id,
                'appointment_id' => $request->input('appointment_id'),
                'user_id' => $request->input('user_id')
            ]);
            $appointment->delete();
            $response = [
                'success' => true,
                'message' => "Queued Successfully",
                'data' => $queue,
            ];
            // Send an Email to User
            $mail = Mail::to($queue->user->email)->send(new QueuingMail($queue));
            return response()->json($response, 201);
        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
            ];
            return response()->json($errors, 500);
        }
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
        try {
            $queue = Queue::find($id);
            if ($queue) {
                $queue->delete();
                $data = [
                    'success' => true,
                    'data' => $queue,
                    'message' => 'Queued Successfully.',
                ];
                return response()->json($data, 200);
            }
            $response = [
                'success' => false,
                'message' => 'Queue not found',
            ];
            // Send an Email to User when Queuing is completed
            return response()->json($response, 404);
        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
            ];
            return response()->json($errors, 500);
        }
    }
}
