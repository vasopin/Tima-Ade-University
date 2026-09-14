<?php

namespace App\Http\Controllers;

class HrWorkspaceController extends Controller
{
    private function authorizeHR(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isHROfficer()), 403, 'HR access required.');
    }

    public function recruitment()
    {
        $this->authorizeHR();
        return view('hr.recruitment');
    }

    public function performance()
    {
        $this->authorizeHR();
        return view('hr.performance');
    }

    public function attendance()
    {
        $this->authorizeHR();
        return view('hr.attendance');
    }
}
