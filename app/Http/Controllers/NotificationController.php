<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request) {
        $notifications = $request->user()->notifications()->latest()->get();
        return response()->json(['success' => true, 'notifications' => $notifications]);
    }

    public function unread(Request $request) {
        $unread = $request->user()->unreadNotifications()->latest()->get();
        return response()->json($unread);
    }

    public function markAsRead(Request $request, $id) {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function markAllAsRead(Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function destroy(Request $request, $id) {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }
}
