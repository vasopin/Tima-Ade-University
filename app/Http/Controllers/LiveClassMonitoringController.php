<?php

namespace App\Http\Controllers;

use App\Models\LiveClass;
use Illuminate\Support\Facades\Auth;

class LiveClassMonitoringController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user && $user->isAdmin(), 403, 'Administrator access required.');

        $liveClasses = LiveClass::with([
            'teacher',
            'subject',
            'schoolClass',
            'participants.user',
            'activities.actor',
        ])->whereIn('status', ['live', 'ended'])
            ->latest('started_at')
            ->get();

        return view('admin.live-class-monitoring', compact('liveClasses'));
    }
}
