<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $routeName = request()->route()?->getName();
            $user = Auth::user();

            if ($user->isAdmin() || $user->isSuperAdmin()) {
                if ($routeName === 'admin.login' || $routeName === 'login') {
                    return redirect()->route('hemis');
                }

                return redirect()->route('hemis');
            }

            if ($routeName === 'admin.login' && $this->isAdministrationRole($user)) {
                    return redirect()->route($this->resolveWelcomeRouteName($user));
            }

            if ($routeName === 'university.login' && ! (Auth::user()->isTeacher() || Auth::user()->isStudent() || Auth::user()->isParent())) {
                    return redirect()->route($this->resolveWelcomeRouteName($user))->with('portal_access_error', 'You cannot log in through the University Portal Sign-In. Please use the University Admin Sign-In.');
            }

                return redirect()->route($this->resolveWelcomeRouteName($user));
        }

        $portalContext = match (request()->route()?->getName()) {
            'admin.login' => 'admin',
            'university.login' => 'university',
            default => null,
        };

        $intendedPath = parse_url((string) session('url.intended', ''), PHP_URL_PATH) ?: '';
        $portalContext ??= str_starts_with($intendedPath, '/hemis')
            ? 'admin'
            : (str_starts_with($intendedPath, '/exams/results') ? 'university' : null);

        if ($portalContext) {
            session(['login.portal_context' => $portalContext]);
        }

        return view('auth.login', compact('portalContext'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Please enter your email.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ]);

        $email = Str::lower(trim($credentials['email']));
        $password = $credentials['password'];


        $throttleKey = Str::transliterate($email . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $user = User::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'auth' => 'Your email or password is incorrect. Please check your details and try again.',
            ]);
        }

        if (! $user->is_active || $user->status === 'inactive') {
            Auth::logout();
            $request->session()->invalidate();
            throw ValidationException::withMessages([
                'email' => 'Your account is currently inactive. Please contact the administrator.',
            ]);
        }

        if ($user->status === 'suspended') {
            Auth::logout();
            $request->session()->invalidate();
            throw ValidationException::withMessages([
                'email' => 'Your account has been suspended due to administrative policies. Please reach out to admissions or support.',
            ]);
        }

        if ($user->status === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            throw ValidationException::withMessages([
                'email' => 'Your registration is pending approval by the school administration. You will be notified once active.',
            ]);
        }

        $portalContext = match ($request->route()?->getName()) {
            'admin.login.post', 'legacy.admin.login.post' => 'admin',
            'university.login.post', 'legacy.university.login.post' => 'university',
            default => $request->session()->get('login.portal_context'),
        };
        $isAdministrationRole = $this->isAdministrationRole($user);
        $isUniversityRole = $user->isTeacher() || $user->isStudent() || $user->isParent();

        if (($portalContext === 'admin' && ! $isAdministrationRole) || ($portalContext === 'university' && ! $isUniversityRole)) {
            $message = $portalContext === 'admin'
                ? 'Please use the University Portal Sign-In.'
                : 'You cannot log in through the University Portal Sign-In. Please use the University Admin Sign-In.';

            return redirect()->route($portalContext === 'admin' ? 'admin.login' : 'university.login')
                ->withInput($request->except('password'))
                ->with('portal_access_error', $message);
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->forget('login.portal_context');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Welcome back, ' . $user->name . '!',
            ]);
        }

        $intendedPath = parse_url((string) session('url.intended', ''), PHP_URL_PATH) ?: '';
        $isAdministrationPath = str_starts_with($intendedPath, '/hemis') || str_starts_with($intendedPath, '/admin/applications');
        $isUniversityPath = str_starts_with($intendedPath, '/exams/results');
        $welcomeRoute = $this->resolveWelcomeRouteName($user);
        $portalRoute = $portalContext === 'admin'
            ? route($user->isAdmin() || $user->isSuperAdmin() ? 'hemis' : $welcomeRoute)
            : ($portalContext === 'university' ? route($user->isAdmin() || $user->isSuperAdmin() ? 'hemis' : $welcomeRoute) : null);
        $redirectRoute = ($isAdministrationRole && $isUniversityPath) || (!$isAdministrationRole && $isAdministrationPath)
            ? ($isAdministrationRole ? route($user->isAdmin() || $user->isSuperAdmin() ? 'hemis' : $welcomeRoute) : route($welcomeRoute))
            : null;
        $defaultRoute = $user->isAdmin() || $user->isSuperAdmin() ? route('hemis') : route($welcomeRoute);

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return ($portalRoute ? redirect($portalRoute) : ($redirectRoute ? redirect($redirectRoute) : redirect()->intended($defaultRoute)))
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return redirect()->route($welcomeRoute)
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    public function showRegister()
    {
        return redirect()->route('login')->with('info', 'Account creation is managed by university administrators.');
    }

    private function resolveWelcomeRouteName(User $user): string
    {
        if ($user->isStudent()) {
            return 'student.welcome';
        }

        if ($user->isTeacher()) {
            return 'teacher.welcome';
        }

        if ($user->isParent()) {
            return 'parent.welcome';
        }

        if ($user->isStaff()) {
            return 'staff.welcome';
        }

        if ($user->isExecutive()) {
            return 'executive.welcome';
        }

        if ($user->isDean()) {
            return 'dean.welcome';
        }

        if ($user->isDepartmentHead()) {
            return 'department-head.welcome';
        }

        if ($user->isAcademicAdvisor()) {
            return 'academic-advisor.welcome';
        }

        if ($user->isAdmissionsOfficer()) {
            return 'admissions_officer.welcome';
        }

        if ($user->isFinanceOfficer()) {
            return 'finance_officer.welcome';
        }

        if ($user->isRegistrar()) {
            return 'registrar.welcome';
        }

        if ($user->isHROfficer()) {
            return 'hr_officer.welcome';
        }

        if ($user->isLibrarian()) {
            return 'librarian.welcome';
        }

        return 'dashboard.welcome';
    }

    private function isAdministrationRole(User $user): bool
    {
        return $user->isAdmin()
            || $user->isStaff()
            || $user->isExecutive()
            || $user->isDean()
            || $user->isDepartmentHead()
            || $user->isAcademicAdvisor()
            || $user->isAdmissionsOfficer()
            || $user->isFinanceOfficer()
            || $user->isRegistrar()
            || $user->isHROfficer()
            || $user->isLibrarian();
    }

    private function isSpecializedAdministrationRole(User $user): bool
    {
        return $user->isExecutive()
            || $user->isDean()
            || $user->isDepartmentHead()
            || $user->isAcademicAdvisor()
            || $user->isAdmissionsOfficer()
            || $user->isFinanceOfficer()
            || $user->isRegistrar()
            || $user->isHROfficer()
            || $user->isLibrarian();
    }

    public function register(Request $request)
    {
        return redirect()->route('login')->with('info', 'Account creation is managed by university administrators.');
    }

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = Str::lower(trim($request->string('email')->toString()));
        $user = User::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
        if (!$user) {
            return back()->with('status', 'If your email is registered in Tima-Ade University, you will receive a password reset link.');
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email'      => $request->email,
                'token'      => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Store reset URL in session or flash for ease of testing/reset in local dev, or standard status message
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
        
        \Illuminate\Support\Facades\Log::info("Tima-Ade University Password Reset for {$request->email}: {$resetUrl}");

        return back()->with('status', 'Password reset instructions have been generated. (In local environment, check logs or use the link below)')
            ->with('dev_reset_url', $resetUrl);
    }

    public function showResetPassword(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            throw ValidationException::withMessages([
                'email' => 'This password reset token is invalid or has expired.',
            ]);
        }

        // Check if token is older than 60 minutes
        if (isset($record->created_at) && now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            throw ValidationException::withMessages([
                'email' => 'This password reset token has expired. Please request a new one.',
            ]);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ]);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return redirect()->route('login')
                ->with('success', 'Your password has been successfully reset! Please sign in with your new password.');
        }

        throw ValidationException::withMessages([
            'email' => 'User not found.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
