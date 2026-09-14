<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Canonical API /user route (stateless API access) — protect with Sanctum
// This is the single source of truth for API clients and tests.
Route::middleware('auth:web')->get('/user', function (\Illuminate\Http\Request $request) {
    $user = $request->user()?->load('role');
    if (! $user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $roleSlug = $user->role?->slug ?? 'student';
    if ($roleSlug === 'super_admin') {
        $roleSlug = 'super-admin';
    } elseif ($roleSlug === 'admin') {
        $roleSlug = 'university-admin';
    }

    return response()->json([
        'id'     => $user->id,
        'name'   => $user->name,
        'email'  => $user->email,
        'role'   => $roleSlug,
        'student_id' => $user->student?->student_id,
        'phone'  => $user->phone,
        'avatar' => $user->avatar,
    ]);
});

// Notifications API (stateless) — protected via Sanctum
use App\Http\Controllers\Api\NotificationController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
});

