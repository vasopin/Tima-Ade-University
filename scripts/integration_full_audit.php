<?php
// Integration audit script: tests key API flows non-destructively and small safe writes (notifications)
// Usage: php scripts/integration_full_audit.php

$base = 'http://localhost:8000';
$cookieJar = __DIR__ . '/.cookiejar_audit.txt';
@unlink($cookieJar);

function req($method, $url, $cookieJar, $data = null, $headers = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
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

function short($r) { if (!is_array($r)) return ''; if (!$r['ok']) return 'ERR: '.$r['error']; return 'HTTP '.$r['status']; }

echo "=== Integration full audit starting\n";

$results = [];

// helper to run a login for given credentials
function doLogin($email, $password, $base, $cookieJar) {
    $out = [];
    $r = req('GET', $base.'/sanctum/csrf-cookie', $cookieJar);
    $out['csrf'] = $r;
    $xsrf = getXsrfFromJar($cookieJar);
    $out['xsrf_present'] = $xsrf?true:false;
    $post = http_build_query(['email'=>$email,'password'=>$password]);
    $headers = ['Content-Type: application/x-www-form-urlencoded'];
    if ($xsrf) $headers[] = 'X-XSRF-TOKEN: '.$xsrf;
    $r = req('POST', $base.'/login', $cookieJar, $post, $headers);
    $out['login'] = $r;
    // fetch user
    $r = req('GET', $base.'/api/user', $cookieJar);
    $out['user'] = $r;
    return $out;
}

// 1) Test super-admin (admin@school.com)
echo "\n-- Testing super-admin (admin@school.com) --\n";
$sa = doLogin('admin@school.com','password',$base,$cookieJar);
echo "CSRF: ".short($sa['csrf'])." xsrf_present:".($sa['xsrf_present']? 'yes':'no')."\n";
echo "Login: ".short($sa['login'])."\n";
echo "User: ".short($sa['user'])."\n";
if ($sa['user']['ok'] && $sa['user']['status']===200) {
    echo "User body: ".substr(trim($sa['user']['body']),0,200)."\n";
}

// 1.a) Notifications flow: create -> list -> mark read -> mark all read
$xsrf = getXsrfFromJar($cookieJar);
if ($xsrf) {
    echo "\nNotifications flow for super-admin\n";
    $payload = json_encode(['title'=>'Integration Test Notice','body'=>'Audit notice','role'=>'all']);
    $r = req('POST', $base.'/api/notifications', $cookieJar, $payload, ['Content-Type: application/json', 'X-XSRF-TOKEN: '.$xsrf]);
    echo "Create: ".short($r)."\n";
    $created = null;
    if ($r['ok'] && $r['status']===201) $created = json_decode($r['body'], true);
    // list
    $r = req('GET', $base.'/api/notifications', $cookieJar);
    echo "List: ".short($r)."\n";
    if ($r['ok'] && $r['status']===200) echo "List sample: ".substr(trim($r['body']),0,200)."\n";
    // try mark read if created
    if ($created && isset($created['id'])) {
        $id = $created['id'];
        $r = req('POST', $base.'/api/notifications/'.$id.'/read', $cookieJar, '', ['X-XSRF-TOKEN: '.$xsrf]);
        echo "MarkRead: ".short($r)."\n";
    }
    // mark all
    $r = req('POST', $base.'/api/notifications/read-all', $cookieJar, '', ['X-XSRF-TOKEN: '.$xsrf]);
    echo "MarkAllRead: ".short($r)."\n";
}

// 1.b) Try GET api/sections (web route)
$r = req('GET', $base.'/api/sections', $cookieJar);
echo "\n/api/sections: ".short($r)."\n";
if ($r['ok'] && $r['status']===200) echo "Sections sample: ".substr(trim($r['body']),0,200)."\n";

// 2) Test teacher login and user role
@unlink($cookieJar);
$teacher = doLogin('teacher1@school.com','password',$base,$cookieJar);
echo "\n-- Testing teacher (teacher1@school.com) --\n";
echo "Login: ".short($teacher['login'])." User: ".short($teacher['user'])."\n";
if ($teacher['user']['ok']) echo "User body: ".substr(trim($teacher['user']['body']),0,200)."\n";

// 3) Test student login
@unlink($cookieJar);
$student = doLogin('student1@school.com','password',$base,$cookieJar);
echo "\n-- Testing student (student1@school.com) --\n";
echo "Login: ".short($student['login'])." User: ".short($student['user'])."\n";
if ($student['user']['ok']) echo "User body: ".substr(trim($student['user']['body']),0,200)."\n";

// 4) Quick smoke of reports endpoints (if present)
@unlink($cookieJar);
$sa2 = doLogin('admin@school.com','password',$base,$cookieJar);
echo "\n-- Reports endpoints smoke (authenticated) --\n";
$paths = ['/reports/attendance','/reports/fees','/reports/academic'];
foreach ($paths as $p) {
    $r = req('GET', $base.$p, $cookieJar);
    echo "$p: ".short($r)."\n";
}

echo "\n=== Audit complete\n";

// exit nonzero if any critical failures detected? We'll not exit nonzero to preserve interactive runs.
exit(0);
