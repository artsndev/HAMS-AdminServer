<?php

namespace App\Http\Controllers\API\Admin\Auth;

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
        Auth::guard('admin')->logout();
        return response()->json(['message' => 'Admin successfully signed out'], 200);
    }
}
