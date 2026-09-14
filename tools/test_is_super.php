<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;

// Setup Eloquent outside Laravel using project's .env DB settings
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$capsule = new Capsule();
$capsule->addConnection([
    'driver' => getenv('DB_CONNECTION') ?: 'mysql',
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'database' => getenv('DB_DATABASE') ?: 'Tima-Ade University',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$user = User::where('email', 'superadmin@school.com')->first();
if (!$user) { echo "NO_USER\n"; exit(1); }
echo "isSuperAdmin: " . ($user->isSuperAdmin() ? 'YES' : 'NO') . "\n";
echo "isParent: " . ($user->isParent() ? 'YES' : 'NO') . "\n";
