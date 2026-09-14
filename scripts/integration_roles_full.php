<?php
// Roles E2E audit: class/subject/teacher CRUD, parent registration, permission checks
$base = 'http://localhost:8000';
$adminJar = __DIR__ . '/.cookie_admin.txt';
$teacherJar = __DIR__ . '/.cookie_teacher.txt';
$parentJar = __DIR__ . '/.cookie_parent.txt';
@unlink($adminJar); @unlink($teacherJar); @unlink($parentJar);

function req($method, $url, $cookieJar, $data = null, $headers = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
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
function getXsrf($jar) {
    if (!file_exists($jar)) return null;
    $lines = file($jar, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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
function short($r){ if (!$r['ok']) return 'ERR: '.$r['error']; return 'HTTP '.$r['status']; }

echo "=== Roles E2E starting\n";

// Admin login
$r = req('GET', $base.'/sanctum/csrf-cookie', $adminJar);
$xsrf = getXsrf($adminJar);
$post = http_build_query(['email'=>'admin@school.com','password'=>'password']);
$headers = ['Content-Type: application/x-www-form-urlencoded', 'X-XSRF-TOKEN: '.$xsrf];
$r = req('POST', $base.'/login', $adminJar, $post, $headers);
echo "Admin login: ".short($r)."\n";
// refresh csrf
req('GET', $base.'/sanctum/csrf-cookie', $adminJar);
$xsrf = getXsrf($adminJar);

// Create Class
$clsName = 'E2E Class '.time();
$fields = ['name'=>$clsName,'grade_level'=>'X','description'=>'E2E','is_active'=>1,'sections[]'=>'X-A','sections[]'=>'X-B'];
$postBody = http_build_query($fields);
$headers = ['Content-Type: application/x-www-form-urlencoded','X-XSRF-TOKEN: '.$xsrf];
$r = req('POST', $base.'/classes', $adminJar, $postBody, $headers);
echo "Create class: ".short($r)."\n";
// Find class id
$idx = req('GET', $base.'/classes', $adminJar);
$foundId = null;
if ($idx['ok']) {
    if (strpos($idx['body'], $clsName) !== false) {
        // crude parse: look for /classes/{id}
        if (preg_match('/classes\/(\d+)/', $idx['body'], $m)) { $foundId = $m[1]; }
    }
}
echo "Class index contains new class: ".($foundId?"yes (id=$foundId)":"no")."\n";

// Update class (if found)
if ($foundId) {
    $postBody = http_build_query(['_method'=>'PUT','name'=>$clsName.' Updated','grade_level'=>'Y']);
    $r = req('POST', $base.'/classes/'.$foundId, $adminJar, $postBody, $headers);
    echo "Update class: ".short($r)."\n";
    // Delete class
    $postBody = http_build_query(['_method'=>'DELETE']);
    $r = req('POST', $base.'/classes/'.$foundId, $adminJar, $postBody, $headers);
    echo "Delete class: ".short($r)."\n";
}

// Subject CRUD
$subCode = 'E2E'.rand(1000,9999);
$postBody = http_build_query(['name'=>'E2E Subject '.time(),'code'=>$subCode,'description'=>'desc','is_active'=>1]);
$r = req('POST', $base.'/subjects', $adminJar, $postBody, $headers);
echo "Create subject: ".short($r)."\n";
// find subject id from listing
$idx = req('GET', $base.'/subjects', $adminJar);
$subId = null; if ($idx['ok'] && strpos($idx['body'],$subCode)!==false) { if (preg_match('/subjects\/(\d+)/', $idx['body'],$m)) $subId=$m[1]; }
echo "Subject present: ".($subId?"yes (id=$subId)":"no")."\n";
if ($subId) {
    $postBody = http_build_query(['_method'=>'PUT','name'=>'E2E Subject u','code'=>$subCode,'is_active'=>1]);
    $r = req('POST', $base.'/subjects/'.$subId, $adminJar, $postBody, $headers);
    echo "Update subject: ".short($r)."\n";
    $postBody = http_build_query(['_method'=>'DELETE']);
    $r = req('POST', $base.'/subjects/'.$subId, $adminJar, $postBody, $headers);
    echo "Delete subject: ".short($r)."\n";
}

// Create Teacher
$teachEmail = 'e2e_teacher_'.time().'@example.test';
$postBody = http_build_query(['name'=>'E2E Teacher','email'=>$teachEmail,'password'=>'pass123','phone'=>'000','employee_id'=>'EMP'.rand(100,999),'qualification'=>'Q','specialization'=>'S','joining_date'=>date('Y-m-d')]);
$r = req('POST', $base.'/teachers', $adminJar, $postBody, $headers);
echo "Create teacher: ".short($r)."\n";

// Create Parent via public register (must use different cookie jar as guest)
// Ensure admin session not used
@unlink($parentJar);
$r = req('GET', $base.'/sanctum/csrf-cookie', $parentJar);
$xsrfP = getXsrf($parentJar);
$post = http_build_query(['name'=>'E2E Parent','email'=>'e2e_parent_'.time().'@example.test','phone'=>'111','role_slug'=>'parent','password'=>'parentpass','password_confirmation'=>'parentpass','terms'=>'1']);
$headersP = ['Content-Type: application/x-www-form-urlencoded','X-XSRF-TOKEN: '.$xsrfP];
$r = req('POST', $base.'/register', $parentJar, $post, $headersP);
echo "Parent register: ".short($r)."\n";
// Parent should be logged in automatically; access dashboard or child view
$r = req('GET', $base.'/dashboard', $parentJar);
echo "Parent dashboard access: ".short($r)."\n";

// Teacher login and permission check
// login teacher created earlier via teacher email
$r = req('GET', $base.'/sanctum/csrf-cookie', $teacherJar);
$xsrfT = getXsrf($teacherJar);
$post = http_build_query(['email'=>'teacher1@school.com','password'=>'password']);
$headersT = ['Content-Type: application/x-www-form-urlencoded','X-XSRF-TOKEN: '.$xsrfT];
$r = req('POST', $base.'/login', $teacherJar, $post, $headersT);
echo "Teacher login: ".short($r)."\n";
// Teacher access students index
$r = req('GET', $base.'/students', $teacherJar);
echo "Teacher view students: ".short($r)."\n";
// Teacher attempt to delete class (should be protected ideally but currently will succeed if no role check). We'll attempt and record result.
$attemptDelete = req('POST', $base.'/classes/1', $teacherJar, http_build_query(['_method'=>'DELETE']), ['Content-Type: application/x-www-form-urlencoded','X-XSRF-TOKEN: '.getXsrf($teacherJar)]);
echo "Teacher delete class (permission check): ".short($attemptDelete)."\n";

// Reports smoke as admin (re-login admin if necessary)
$r = req('GET', $base.'/reports/attendance', $adminJar);
echo "/reports/attendance: ".short($r)."\n";
$r = req('GET', $base.'/reports/fees', $adminJar);
echo "/reports/fees: ".short($r)."\n";
$r = req('GET', $base.'/reports/academic', $adminJar);
echo "/reports/academic: ".short($r)."\n";

echo "=== Roles E2E complete\n";
exit(0);
