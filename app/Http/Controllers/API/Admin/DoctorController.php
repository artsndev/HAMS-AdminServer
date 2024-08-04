<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $doctor = Doctor::latest()->get();
            $data = [
                'success' => true,
                'data' => $doctor,
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
        try {
            $doctor = Doctor::find($id);
            if(!$doctor) {
                $data = [
                    'success' => false,
                    'message' => 'Doctor not found',
                ];
                return response()->json($data, 404);
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|unique:doctors|email',
            ]);
            if ($validator->fails()) {
                $data = [
                    'success' => false,
                    'error' => $validator->errors(),
                ];
                return response()->json($data, 400);
            }
            $doctor->update([
                'name'=> $request->input('name'),
                'email' => $request->input('email'),
            ]);
            $result = [
                'success' => true,
                'message' => 'Updated Successfully',
                'data' => $doctor,
            ];
            return response()->json($result, 200);
        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
            ];
            return response()->json($errors, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $doctor = Doctor::find($id);
            if ($doctor) {
                $doctor->delete();
                $data = [
                    'success' => true,
                    'message' => 'Doctor was successfully destroyed.',
                    'deleted_doctor' => $doctor,
                ];
                return response()->json($data, 202);
            }
            $data = [
                'success' => false,
                'message' => 'Doctors doesn\'t  Exists',
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
