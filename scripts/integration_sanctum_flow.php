<?php
// Simple CLI integration test that exercises Sanctum SPA flow against running Laravel server.
// Usage: php scripts/integration_sanctum_flow.php

$base = 'http://localhost:8000';
$cookieJar = __DIR__ . '/.cookiejar.txt';
@unlink($cookieJar);

function curl_get($url, $cookieJar) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $res = curl_exec($ch);
    if ($res === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['ok'=>false,'error'=>$err];
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($res, 0, $header_len);
    $body = substr($res, $header_len);
    curl_close($ch);
    return ['ok'=>true,'status'=>$status,'header'=>$header,'body'=>$body];
}

function curl_post($url, $postFields, $cookieJar, $headers = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    if ($res === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['ok'=>false,'error'=>$err];
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($res, 0, $header_len);
    $body = substr($res, $header_len);
    curl_close($ch);
    return ['ok'=>true,'status'=>$status,'header'=>$header,'body'=>$body];
}

$output = [];

// 1) GET /sanctum/csrf-cookie
$r = curl_get($base . '/sanctum/csrf-cookie', $cookieJar);
$output['csrf'] = $r;

// Parse cookie jar for XSRF-TOKEN
$xsrf = null;
if (file_exists($cookieJar)) {
    $lines = file($cookieJar, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $ln) {
        if ($ln[0] === '#') continue;
        $parts = preg_split('/\s+/', $ln);
        // Netscape cookie format: domain, flag, path, secure, expiration, name, value
        if (count($parts) >= 7) {
            $name = $parts[5];
            $value = $parts[6];
            if ($name === 'XSRF-TOKEN') {
                $xsrf = urldecode($value);
                break;
            }
        }
    }
}
$output['xsrf_present'] = $xsrf !== null;

// 2) POST /login
$post = http_build_query(['email'=>'admin@school.com','password'=>'password']);
$headers = ['Content-Type: application/x-www-form-urlencoded'];
if ($xsrf) $headers[] = 'X-XSRF-TOKEN: ' . $xsrf;
$r = curl_post($base . '/login', $post, $cookieJar, $headers);
$output['login'] = $r;

// 3) GET /api/user
$r = curl_get($base . '/api/user', $cookieJar);
$output['user'] = $r;

// 4) GET /api/notifications
$r = curl_get($base . '/api/notifications', $cookieJar);
$output['notifications'] = $r;

// 5) POST /logout (refresh CSRF cookie first to ensure token matches session)
$r = curl_get($base . '/sanctum/csrf-cookie', $cookieJar);
// re-parse XSRF-TOKEN
$xsrf = null;
if (file_exists($cookieJar)) {
    $lines = file($cookieJar, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $ln) {
        if ($ln[0] === '#') continue;
        $parts = preg_split('/\s+/', $ln);
        if (count($parts) >= 7) {
            $name = $parts[5];
            $value = $parts[6];
            if ($name === 'XSRF-TOKEN') {
                $xsrf = urldecode($value);
                break;
            }
        }
    }
}
$r = curl_post($base . '/logout', '', $cookieJar, $xsrf ? ['X-XSRF-TOKEN: ' . $xsrf] : []);
$output['logout'] = $r;

// Print concise report
function short($r) {
    if (!is_array($r)) return '';
    if (!$r['ok']) return 'ERR: ' . ($r['error'] ?? 'unknown');
    return 'HTTP ' . ($r['status'] ?? '??');
}

echo "Integration test results:\n";
echo "CSRF: " . short($output['csrf']) . " (XSRF present: " . ($output['xsrf_present'] ? 'yes' : 'no') . ")\n";
echo "Login: " . short($output['login']) . "\n";
echo "User: " . short($output['user']) . "\n";
echo "Notifications: " . short($output['notifications']) . "\n";
echo "Logout: " . short($output['logout']) . "\n";

// If responses have body and are JSON, try to pretty-print user and notifications
if (isset($output['user']['body'])) {
    $body = trim($output['user']['body']);
    if ($body) {
        echo "\n/user body:\n" . substr($body,0,2000) . "\n";
    }
}
if (isset($output['notifications']['body'])) {
    $body = trim($output['notifications']['body']);
    if ($body) {
        echo "\n/notifications body:\n" . substr($body,0,2000) . "\n";
    }
}

// Exit with non-zero if any critical step failed
$fail = false;
if (!($output['csrf']['ok'] && $output['csrf']['status']>=200 && $output['csrf']['status']<300)) $fail = true;
if (!($output['login']['ok'] && ($output['login']['status']===200 || $output['login']['status']===302))) $fail = true;
if (!($output['user']['ok'] && $output['user']['status']===200)) $fail = true;
if (!($output['notifications']['ok'] && $output['notifications']['status']>=200 && $output['notifications']['status']<500)) $fail = true;
exit($fail?1:0);
