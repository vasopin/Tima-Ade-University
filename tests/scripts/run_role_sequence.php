<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

function send($kernel, $method, $uri, $user = null, $headers = []) {
    $request = Illuminate\Http\Request::create($uri, $method);
    foreach ($headers as $k => $v) $request->headers->set($k, $v);
    if ($user) {
        auth()->setUser($user);
    }
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    echo "$method $uri -> $status\n";
    $kernel->terminate($request, $response);
}

// 1. unauthenticated access to /dashboard
send($kernel, 'GET', '/dashboard');

// 2. unauthenticated access to /students
send($kernel, 'GET', '/students');

// 3. seed DB and get admin user
$console = $app->make(Illuminate\Contracts\Console\Kernel::class);
$console->call('db:seed');
$admin = App\Models\User::where('email', 'admin@school.com')->first();
if (!$admin) { echo "No admin user\n"; exit(1); }

// acting as admin, access /dashboard and /students
send($kernel, 'GET', '/dashboard', $admin);
send($kernel, 'GET', '/students', $admin);

// acting as teacher to /api/user
$teacher = App\Models\User::where('email', 'teacher1@school.com')->first();
if (!$teacher) { echo "No teacher user\n"; exit(1); }

send($kernel, 'GET', '/api/user', $teacher, ['Accept' => 'application/json']);
