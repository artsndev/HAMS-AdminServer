<?php

namespace App\Http\Controllers\API\User;

use App\Models\Queue;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
            $pending_appointments = Appointment::where('user_id', Auth::user()->id)->get()->count();
            $processing_appointments = Appointment::onlyTrashed()->where('user_id', Auth::user()->id)->get()->count();
            $completed_appointments = Queue::onlyTrashed()->where('user_id', Auth::user()->id)->get()->count();
            $data = [
                'pending_appointments' => $pending_appointments,
                'processing_appointments' => $processing_appointments,
                'completed_appointments' => $completed_appointments,
            ];
            return response()->json($data, 200);
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
