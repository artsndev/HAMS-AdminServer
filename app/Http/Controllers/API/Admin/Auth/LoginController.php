<?php

namespace App\Http\Controllers\API\Admin\Auth;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Login a nexisting account in api.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);
            if ($validator->fails()) {
                $errors = [
                    'success' => false,
                    'errors' => $validator->errors(),
                ];
                return response()->json($errors, 200);
            }
            $errors = [$this->username() => trans('auth.failed')];
            $admin = \App\Models\Admin::where($this->username(), $request->{$this->username()})->first();
            if ($admin && !Hash::check($request->password, $admin->password)) {
                $errors = [
                    'message' => 'The provided password is incorrect.'
                ];
                return response()->json($errors, 200);
            }
            // Attempt to authenticate the user
            $credentials = $request->only('email', 'password');
            if (!Auth::guard('admin')->attempt($credentials)) {
                $errors = [
                    'success' => false,
                    'message' => 'These credentials do not match our records.',
                ];
                return response()->json($errors, 200);
            }
            $admin = Auth::guard('admin')->user();
            $adminToken = JWTAuth::fromUser($admin);
            $response = [
                'success' => true,
                'data' => [
                    'adminToken' => $adminToken,
                    'admin' => $admin,
                ],
                'message' => 'Successfully logged in.',
            ];
            return response()->json($response, 201);
        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
            ];
            return response()->json($errors, 500);
        }
    }
}
