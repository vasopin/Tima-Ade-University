<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\LmsAnalyticsService;

class LmsAnalyticsController extends Controller
{
    public function __construct(private LmsAnalyticsService $analytics) {}

    public function student()
    {
        $user = auth()->user();
        abort_unless($user->isStudent() && $user->student, 403);
        return view('analytics.student', $this->analytics->student($user->student));
    }

    public function teacher()
    {
        $user = auth()->user();
        abort_unless($user->isTeacher(), 403);
        return view('analytics.teacher', ['report' => $this->analytics->teacher($user)]);
    }
}
