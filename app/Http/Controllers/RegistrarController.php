<?php

namespace App\Http\Controllers;

class RegistrarController extends Controller
{
    private function authorizeRegistrar(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || $user->isRegistrar()), 403, 'Registrar access required.');
    }

    public function enrollment()
    {
        $this->authorizeRegistrar();
        return app(EnrollmentController::class)->history(request());
    }

    public function certificates()
    {
        $this->authorizeRegistrar();
        return view('registrar.certificates');
    }

    public function transferCredits()
    {
        $this->authorizeRegistrar();
        return view('registrar.transfer-credits');
    }
}
