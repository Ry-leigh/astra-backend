<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Retrieve user notification and general preferences.
     */
    public function getPreferences(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'preferences' => $user->preferences,
        ]);
    }

    /**
     * Update user notification preferences.
     */
    public function updatePreferences(Request $request)
    {
        $data = $request->validate([
            'notify_email' => 'boolean',
            'notify_in_app' => 'boolean',
        ]);

        $request->user()->preferences()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json([
            'message' => 'Preferences updated successfully.',
            'preferences' => $request->user()->fresh()->preferences,
        ]);
    }

    /**
     * Allow user to securely change their password.
     */
    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json([
                'error' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
    }
}
