<?php

namespace App\Http\Controllers\API\Doctor\Auth;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Register new Doctor.
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|unique:doctors|email',
                'password' => 'required|min:8|confirmed',
            ]);
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'errors' => $validator->errors(),
                ];
                return response()->json($response, 200);
            }
            $doctor = Doctor::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);
            $doctorToken = JWTAuth::fromUser($doctor);
            // $mail = Mail::to($doctor['email'])->send(new WelcomeMail($doctor));
            $response = [
                'success' => true,
                'data' => [
                    'doctorToken' => $doctorToken,
                    'doctor_info' => $doctor,
                ],
                'message' => "User registered successfully",
                'mail_message' => 'Mail sent successfully',
            ];
            Auth::guard('doctor')->login($doctor);
            return response()->json($response, 201)->header('Authorization', 'Bearer ' . $doctorToken);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }
}
