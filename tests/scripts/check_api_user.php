<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
// Run the seeders to ensure users exist
$kernel->call('db:seed');

// Boot the HTTP kernel
$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Find teacher user
$userModel = $app->make(\App\Models\User::class);
$teacher = $userModel->where('email', 'teacher1@school.com')->first();
if (! $teacher) {
    echo "Teacher user not found\n";
    exit(1);
}

// Set the current user in the container/auth
// Try multiple ways to set the authenticated user
if (function_exists('auth')) {
    auth()->setUser($teacher);
}

$request = Request::create('/api/user', 'GET');
// add Accept header for JSON
$request->headers->set('Accept', 'application/json');

$response = $httpKernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Body: " . $response->getContent() . "\n";

$httpKernel->terminate($request, $response);
