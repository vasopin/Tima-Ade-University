<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Facility;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->check() && auth()->user()->isSuperAdmin(), 403, 'Super Admin access required.');
            return $next($request);
        });
    }

    public function tenants()
    {
        return view('super-admin.tenants', [
            'users' => User::count(),
            'roles' => Role::withCount('users')->orderBy('name')->get(),
            'departments' => Employee::query()->whereNotNull('department')->distinct()->orderBy('department')->pluck('department'),
            'classes' => SchoolClass::count(),
            'facilities' => Facility::count(),
        ]);
    }

    public function roles()
    {
        return view('super-admin.roles', [
            'roles' => Role::withCount('users')->with([
                'users' => fn ($query) => $query->select(['id', 'role_id', 'name', 'email', 'status'])->orderBy('name'),
            ])->orderBy('name')->get(),
        ]);
    }

    public function health()
    {
        $failedJobs = null;
        try {
            $failedJobs = DB::table('failed_jobs')->count();
        } catch (\Throwable $exception) {
            $failedJobs = 'Unavailable';
        }

        return view('super-admin.health', [
            'database' => $this->databaseStatus(),
            'environment' => app()->environment(),
            'debug' => config('app.debug'),
            'queue' => config('queue.default'),
            'failedJobs' => $failedJobs,
            'storageFree' => disk_free_space(storage_path()),
            'storagePath' => storage_path(),
            'integrations' => [
                'Mail transport configured' => filled(config('mail.default')),
                'Broadcasting configured' => filled(config('broadcasting.default')),
                'Cache configured' => filled(config('cache.default')),
            ],
        ]);
    }

    private function databaseStatus(): string
    {
        try {
            DB::connection()->getPdo();
            return 'Connected';
        } catch (\Throwable $exception) {
            return 'Unavailable';
        }
    }
}