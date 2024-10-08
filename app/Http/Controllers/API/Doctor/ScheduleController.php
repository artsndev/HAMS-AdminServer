<?php

namespace App\Http\Controllers\API\Doctor;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $schedule = Schedule::with('doctor')->withTrashed()->where('doctor_id', Auth::user()->id)->latest()->get();
            $data = [
                'success' => true,
                'data' => $schedule,
            ];
            return response()->json($data, 200);
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
            $request->merge([
                'schedule_time' => str_replace('T', ' ', $request->input('schedule_time'))
            ]);
            $validator = Validator::make($request->all(), [
                'schedule_time' => [
                    'required','date_format:Y-m-d H:i', function ($attribute, $value, $fail) use ($request) {
                        $doctor_id = Auth::user()->id;
                        $existingSchedule = Schedule::where('schedule_time', $value)->where('doctor_id', $doctor_id)->first();
                        if ($existingSchedule) {
                            $fail('You\'ve already selected this schedule time. Try another time.');
                        }
                    },
                ],
            ]);
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'errors' => $validator->errors(),
                ];
                return response()->json($response, 200);
            }
            $schedule = Schedule::create([
                'doctor_id' => Auth::user()->id,
                'schedule_time' => $request->input('schedule_time'),
            ]);
            $response = [
                'success' => true,
                'data' => [
                    'schedule' => $schedule,
                ],
                'message' => "Doctor Scheduled successfully",
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
            $schedule = Schedule::find($id);
            if ($schedule) {
                $schedule->forceDelete();
                $data = [
                    'success' => true,
                    'message' => 'schedule was successfully destroyed.',
                    'deleted_schedule' => $schedule,
                ];
                return response()->json($data, 202);
            }
            $data = [
                'success' => false,
                'message' => 'schedules doesn\'t  Exists',
            ];
            return response()->json($data, 404);
        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
            ];
            return response()->json($errors, 500);
        }
    }
}
