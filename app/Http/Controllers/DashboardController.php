<?php

namespace App\Http\Controllers;

use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = now();
        $date = new DateTime($now);
        $date->setTimezone(new DateTimeZone('Asia/Manila'));

        $greetings = "Good morning";

        return response()->json(['message' => $greetings, 'data' => $user, 'date' => $date]);
    }
}
