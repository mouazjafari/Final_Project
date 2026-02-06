<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;

class FcmTokenController extends Controller
{
    /**
     * Store or update FCM token for the authenticated user
     */
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = Auth::user();
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'FCM token registered successfully',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Remove FCM token (logout)
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $user->fcm_token = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'FCM token removed successfully',
        ]);
    }
}
