<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Capsule\Manager as Capsule;

// Boot Eloquent outside Laravel (simple connection)
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'Tima-Ade University',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$email = 'admin@school.com';
$token = bin2hex(random_bytes(16));
$hashed = password_hash($token, PASSWORD_DEFAULT);

// insert token
Capsule::table('password_reset_tokens')->updateOrInsert(['email' => $email], ['token' => $hashed, 'created_at' => date('Y-m-d H:i:s')]);
echo "Inserted token for $email\n";

$record = Capsule::table('password_reset_tokens')->where('email', $email)->first();
if (!password_verify($token, $record->token)) {
    echo "Token verify failed\n";
    exit(1);
}
echo "Token verify ok\n";

// Update user password
$newPassword = 'newSecurePass123';
Capsule::table('users')->where('email', $email)->update(['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);
Capsule::table('password_reset_tokens')->where('email', $email)->delete();

$user = Capsule::table('users')->where('email', $email)->first();
if (password_verify($newPassword, $user->password)) {
    echo "Password reset and verified OK\n";
    exit(0);
} else {
    echo "Password reset failed\n";
    exit(1);
}
