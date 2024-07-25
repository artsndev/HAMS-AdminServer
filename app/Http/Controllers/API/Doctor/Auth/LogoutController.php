<?php

namespace App\Http\Controllers\API\Doctor\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Session::flush();
        Auth::guard('doctor')->logout();
        return response()->json(['message' => 'Doctor successfully signed out'], 200);
    }
}
