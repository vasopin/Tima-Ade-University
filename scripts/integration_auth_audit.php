<?php
// Integration auth audit: registration, login, logout, CSRF, duplicate, validation, protected routes
$base = 'http://localhost:8000';
$results = [];

function req($method, $url, $cookieJar, $data=null, $headers=[]) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($method === 'POST') curl_setopt($ch, CURLOPT_POST, true);
    if ($data !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    if ($res === false) { $err = curl_error($ch); curl_close($ch); return ['ok'=>false,'error'=>$err]; }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($res, 0, $header_len);
    $body = substr($res, $header_len);
    curl_close($ch);
    return ['ok'=>true,'status'=>$status,'header'=>$header,'body'=>$body];
}
function getXsrfFromJar($cookieJar) {
    if (!file_exists($cookieJar)) return null;
    $lines = file($cookieJar, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $ln) {
        if ($ln[0] === '#') continue;
        $parts = preg_split('/\s+/', $ln);
        if (count($parts) >= 7) {
            $name = $parts[5]; $value = $parts[6];
            if ($name === 'XSRF-TOKEN') return urldecode($value);
        }
    }
    return null;
}
function short($r){ if (!is_array($r)) return ''; if (!$r['ok']) return 'ERR: '.$r['error']; return 'HTTP '.$r['status']; }

echo "=== Auth audit starting\n";

// Ensure DB seeded
// Use XAMPP php explicitly for migrations to ensure PDO drivers (sqlite) are available in CLI
passthru('"C:\\xampp\\php\\php.exe" "'.__DIR__.'/../artisan" migrate:fresh --seed --no-interaction 2>&1');

$roles = ['student','teacher','parent'];
foreach ($roles as $role) {
    echo "\n-- Testing registration for role: $role --\n";
    $jar = __DIR__ . '/.cookie_reg_'.$role.'.txt'; @unlink($jar);
    // get csrf cookie
    $r = req('GET', $base.'/sanctum/csrf-cookie', $jar);
    echo "CSRF: ".short($r)."\n";
    $xsrf = getXsrfFromJar($jar);
    if (!$xsrf) { echo "FAIL: no XSRF token for role $role\n"; $results[] = ["role"=>$role, "status"=>"FAIL", "reason"=>"no_xsrf"]; continue; }
    // attempt invalid password (too short)
    $email = 'e2e_'.$role.'_'.time().'@example.test';
    $post = http_build_query([
        'name' => 'E2E '.$role,
        'email' => $email,
        'phone' => '1234567890',
        'role_slug' => $role,
        'password' => '123',
        'password_confirmation' => '123',
        'terms' => '1',
    ]);
    $headers = ['Content-Type: application/x-www-form-urlencoded', 'X-XSRF-TOKEN: '.$xsrf];
    $r = req('POST', $base.'/register', $jar, $post, $headers);
    echo "Register (short pw): ".short($r)."\n";
    if ($r['ok'] && ($r['status']===200 || $r['status']===302)) {
        // The register route may redirect back on validation failure; check response body for 'password' error
        if (strpos($r['body'], 'password') !== false) { echo "Validation error present (expected)\n"; } else { echo "WARN: short password accepted unexpectedly\n"; }
    }
    // now register with valid password
    $email2 = $email; // reuse
    $post = http_build_query([
        'name' => 'E2E '.$role.' OK',
        'email' => $email2,
        'phone' => '1234567890',
        'role_slug' => $role,
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
        'terms' => '1',
    ]);
    $r = req('POST', $base.'/register', $jar, $post, $headers);
    echo "Register (valid): ".short($r)."\n";
    // after registration, user should be logged in and redirected to dashboard
    $r2 = req('GET', $base.'/dashboard', $jar);
    echo "Dashboard access after register: ".short($r2)."\n";
    // Check DB for user and password hashed
    try {
        $pdo = new PDO('sqlite:'.__DIR__.'/../database/database.sqlite'); $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare('SELECT id, email, password FROM users WHERE email=? LIMIT 1'); $stmt->execute([$email2]); $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $pw = $row['password'];
            $isHashed = (strpos($pw, '$2y$') === 0 || strpos($pw, '$2a$') === 0);
            echo "DB user found id={$row['id']} hashed_password=".($isHashed? 'yes':'no')."\n";
        } else { echo "DB user not found after registration\n"; }
    } catch (Throwable $e) { echo "DB error: " . $e->getMessage() . "\n"; }

    // Duplicate registration attempt should fail
    $r = req('POST', $base.'/sanctum/csrf-cookie', $jar);
    $xsrf = getXsrfFromJar($jar);
    $headers = ['Content-Type: application/x-www-form-urlencoded','X-XSRF-TOKEN: '.$xsrf];
    $post = http_build_query([
        'name' => 'E2E '.$role.' dup',
        'email' => $email2,
        'phone' => '000',
        'role_slug' => $role,
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
        'terms' => '1',
    ]);
    $r = req('POST', $base.'/register', $jar, $post, $headers);
    echo "Duplicate register attempt: ".short($r)."\n";
    if ($r['ok'] && strpos($r['body'],'email') !== false) { echo "Duplicate validation present (expected)\n"; }

    // logout
    $r = req('POST', $base.'/logout', $jar, http_build_query([]), ['X-XSRF-TOKEN: '.getXsrfFromJar($jar)]);
    echo "Logout: ".short($r)."\n";
    // protected route after logout
    $r = req('GET', $base.'/dashboard', $jar);
    echo "Dashboard after logout (should redirect to login): ".short($r)."\n";

    // Login test: missing XSRF header
    @unlink($jar); $jar = __DIR__.'/ .cookie_login_test_'.$role.'.txt';
    @unlink($jar);
    // get csrf cookie but do not include header
    $r = req('GET', $base.'/sanctum/csrf-cookie', $jar);
    $r = req('POST', $base.'/login', $jar, http_build_query(['email'=>'admin@school.com','password'=>'password']), ['Content-Type: application/x-www-form-urlencoded']);
    echo "Login without XSRF header: ".short($r)."\n";
    // now proper login with XSRF
    $r = req('GET', $base.'/sanctum/csrf-cookie', $jar);
    $xsrf = getXsrfFromJar($jar);
    $r = req('POST', $base.'/login', $jar, http_build_query(['email'=>'admin@school.com','password'=>'password']), ['Content-Type: application/x-www-form-urlencoded','X-XSRF-TOKEN: '.$xsrf]);
    echo "Login with XSRF: ".short($r)."\n";
    $r = req('GET', $base.'/api/user', $jar);
    echo "/api/user after login: ".short($r)."\n";
}

echo "\n=== Auth audit complete\n";
exit(0);
