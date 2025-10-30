<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function getPreferences(Request $request) {
        return response()->json($request->user()->preferences);
    }

    public function updatePreferences(Request $request) {
        $data = $request->validate([
            'notify_email' => 'boolean',
            'notify_in_app' => 'boolean',
        ]);

        $request->user()->preferences()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json(['message' => 'Preferences updated successfully']);
    }

    public function changePassword(Request $request) {
        $data = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($data['current_password'], $request->user()->password)) {
            return response()->json(['error' => 'Current password incorrect'], 422);
        }

        $request->user()->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }
}
