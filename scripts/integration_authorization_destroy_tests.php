<?php
// Targeted authorization tests for destructive routes
// Usage: php scripts/integration_authorization_destroy_tests.php

$base = 'http://localhost:8000';
$cookieAdmin = __DIR__ . '/.cookie_admin.txt';
$cookieTeacher = __DIR__ . '/.cookie_teacher.txt';
$cookieStudent = __DIR__ . '/.cookie_student.txt';
@unlink($cookieAdmin); @unlink($cookieTeacher); @unlink($cookieStudent);

function curl_exec_req($opts) {
    $ch = curl_init($opts['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    if (!empty($opts['cookie'])) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $opts['cookie']);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $opts['cookie']);
    }
    if (!empty($opts['post'])) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $opts['post']);
    }
    if (!empty($opts['headers'])) curl_setopt($ch, CURLOPT_HTTPHEADER, $opts['headers']);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
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

function get_xsrf_from_cookie($cookie) {
    if (!file_exists($cookie)) return null;
    $lines = file($cookie, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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

function get_csrf_and_login($base, $cookieFile, $email, $password) {
    // Get CSRF cookie
    $r = curl_exec_req(['url'=>$base . '/sanctum/csrf-cookie', 'cookie'=>$cookieFile]);
    if (!$r['ok']) return [false, "csrf-get-failed: {$r['error']}"];
    $xsrf = get_xsrf_from_cookie($cookieFile);
    $headers = ['Content-Type: application/x-www-form-urlencoded'];
    if ($xsrf) $headers[] = 'X-XSRF-TOKEN: ' . $xsrf;
    $post = http_build_query(['email'=>$email,'password'=>$password]);
    $r2 = curl_exec_req(['url'=>$base . '/login', 'post'=>$post, 'cookie'=>$cookieFile, 'headers'=>$headers]);
    if (!$r2['ok']) return [false, "login-failed: {$r2['error']}"];
    if (!in_array($r2['status'], [200,302])) return [false, "login-status-{$r2['status']}" ];
    return [true, 'ok'];
}

function find_item_action_by_marker($html, $marker, $pattern) {
    // find block containing marker string and then search for form action pattern in that block
    $pos = strpos($html, $marker);
    if ($pos === false) return null;
    // get snippet around marker
    $start = max(0, $pos - 400);
    $len = 800;
    $snip = substr($html, $start, $len);
    if (preg_match($pattern, $snip, $m)) return $m[1];
    return null;
}

$results = [];

// 1) Login admin, teacher, student
[$ok,$msg] = get_csrf_and_login($base, $cookieAdmin, 'admin@school.com', 'password');
if (!$ok) { echo "Admin login failed: $msg\n"; exit(2); }
[$ok,$msg] = get_csrf_and_login($base, $cookieTeacher, 'teacher1@school.com', 'password');
if (!$ok) { echo "Teacher login failed: $msg\n"; exit(2); }
[$ok,$msg] = get_csrf_and_login($base, $cookieStudent, 'student1@school.com', 'password');
if (!$ok) { echo "Student login failed: $msg\n"; exit(2); }

// Helper to create class as admin and return delete action URL
function create_class_and_get_action($base, $cookieAdmin, $name) {
    // Prepare CSRF header
    $xsrf = get_xsrf_from_cookie($cookieAdmin);
    $headers = ['Content-Type: application/x-www-form-urlencoded'];
    if ($xsrf) $headers[] = 'X-XSRF-TOKEN: ' . $xsrf;
    $post = http_build_query(['name'=>$name, 'grade_level'=>'10', 'sections[]'=>'A', 'sections[]'=>'B']);
    $r = curl_exec_req(['url'=>$base . '/classes', 'post'=>$post, 'cookie'=>$cookieAdmin, 'headers'=>$headers]);
    if (!$r['ok']) return [false, 'create-failed: '.$r['error']];
    // If not a redirect (status 302), print debug
    if (isset($r['status']) && $r['status'] !== 302) {
        // try to surface validation errors
        return [false, 'create-unexpected-status-' . $r['status'] . ' body: ' . substr($r['body'],0,1000)];
    }
    // GET classes index and search for name and form action
    $r2 = curl_exec_req(['url'=>$base . '/classes', 'cookie'=>$cookieAdmin]);
    if (!$r2['ok']) return [false, 'index-get-failed: '.$r2['error']];
    $html = $r2['body'];
    // find class card with name and then form action /classes/{id}
    $action = find_item_action_by_marker($html, htmlspecialchars($name), '/action="(?:https?:\/\/[^"]*)?(\/classes\/(\d+))"/');
    if (!$action) {
        // try without html escaping
        $action = find_item_action_by_marker($html, $name, '/action="(?:https?:\/\/[^"]*)?(\/classes\/(\d+))"/');
    }
    if (!$action) {
        file_put_contents(__DIR__ . '/debug_classes.html', $html);
        return [false, 'could-not-find-action; dumped classes HTML to scripts/debug_classes.html'];
    }
    return [true, $action];
}

function create_subject_and_get_action($base, $cookieAdmin, $name, $code) {
    $xsrf = get_xsrf_from_cookie($cookieAdmin);
    $headers = ['Content-Type: application/x-www-form-urlencoded'];
    if ($xsrf) $headers[] = 'X-XSRF-TOKEN: ' . $xsrf;
    $post = http_build_query(['name'=>$name, 'code'=>$code]);
    $r = curl_exec_req(['url'=>$base . '/subjects', 'post'=>$post, 'cookie'=>$cookieAdmin, 'headers'=>$headers]);
    if (!$r['ok']) return [false, 'create-subject-failed: '.$r['error']];
    $r2 = curl_exec_req(['url'=>$base . '/subjects', 'cookie'=>$cookieAdmin]);
    if (!$r2['ok']) return [false, 'subjects-index-get: '.$r2['error']];
    $html = $r2['body'];
    $action = find_item_action_by_marker($html, htmlspecialchars($code), '/action="(?:https?:\/\/[^"]*)?(\/subjects\/(\d+))"/');
    if (!$action) $action = find_item_action_by_marker($html, $code, '/action="(?:https?:\/\/[^"]*)?(\/subjects\/(\d+))"/');
    if (!$action) return [false, 'subject-action-not-found'];
    return [true, $action];
}

function create_teacher_and_get_action($base, $cookieAdmin, $name, $email, $employeeId) {
    $xsrf = get_xsrf_from_cookie($cookieAdmin);
    $headers = ['Content-Type: application/x-www-form-urlencoded'];
    if ($xsrf) $headers[] = 'X-XSRF-TOKEN: ' . $xsrf;
    $post = http_build_query([
        'name'=>$name,'email'=>$email,'password'=>'password','employee_id'=>$employeeId
    ]);
    $r = curl_exec_req(['url'=>$base . '/teachers', 'post'=>$post, 'cookie'=>$cookieAdmin, 'headers'=>$headers]);
    if (!$r['ok']) return [false, 'create-teacher-failed: '.$r['error']];
    $r2 = curl_exec_req(['url'=>$base . '/teachers', 'cookie'=>$cookieAdmin]);
    if (!$r2['ok']) return [false, 'teachers-index-get: '.$r2['error']];
    $html = $r2['body'];
    $action = find_item_action_by_marker($html, htmlspecialchars($employeeId), '/action="(?:https?:\/\/[^"]*)?(\/teachers\/(\d+))"/');
    if (!$action) $action = find_item_action_by_marker($html, $employeeId, '/action="(?:https?:\/\/[^"]*)?(\/teachers\/(\d+))"/');
    if (!$action) return [false, 'teacher-action-not-found'];
    return [true, $action];
}

// Create resources
$ts = time();
[$ok, $classAction] = create_class_and_get_action($base, $cookieAdmin, 'IntegrationClass-' . $ts);
if (!$ok) { echo "Failed to create/find class: $classAction\n"; exit(3); }
$results['class_action'] = $classAction;
[$ok, $subjectAction] = create_subject_and_get_action($base, $cookieAdmin, 'IntegrationSubject-' . $ts, 'INTSUB' . $ts);
if (!$ok) { echo "Failed to create/find subject: $subjectAction\n"; exit(4); }
$results['subject_action'] = $subjectAction;
[$ok, $teacherAction] = create_teacher_and_get_action($base, $cookieAdmin, 'Integration Teacher ' . $ts, "integ_teacher{$ts}@example.com", 'EMP' . $ts);
if (!$ok) { echo "Failed to create/find teacher: $teacherAction\n"; exit(5); }
$results['teacher_action'] = $teacherAction;

// Now test unauthorized delete attempts
$tests = [];
$resources = [
    'class' => $classAction,
    'subject' => $subjectAction,
    'teacher' => $teacherAction,
];

foreach ($resources as $rtype => $actionUrl) {
    echo "Testing resource $rtype at $actionUrl\n";
    // Attempt delete as teacher (unauthorized expected 403)
    $xsrfT = get_xsrf_from_cookie($cookieTeacher);
    $headers = ['Content-Type: application/x-www-form-urlencoded'];
    if ($xsrfT) $headers[] = 'X-XSRF-TOKEN: ' . $xsrfT;
    $post = http_build_query(['_method'=>'DELETE']);
    $r = curl_exec_req(['url'=>$base . $actionUrl, 'post'=>$post, 'cookie'=>$cookieTeacher, 'headers'=>$headers]);
    $unauthStatus = $r['ok'] ? $r['status'] : 0;
    $tests[$rtype]['unauth_teacher_status'] = $unauthStatus;
    $tests[$rtype]['unauth_teacher_ok'] = ($unauthStatus === 403);
    echo "  Teacher delete attempt returned HTTP $unauthStatus\n";

    // Verify resource still present by GETting index and searching for marker
    $indexUrl = $base . '/' . ($rtype === 'class' ? 'classes' : ($rtype === 'subject' ? 'subjects' : 'teachers'));
    $r2 = curl_exec_req(['url'=>$indexUrl, 'cookie'=>$cookieTeacher]);
    $html = $r2['ok'] ? $r2['body'] : '';
    $stillPresent = (strpos($html, basename($actionUrl)) !== false) || (strpos($html, htmlspecialchars(basename($actionUrl))) !== false);
    // Fallback: check presence by searching for the unique marker used when creating
    if (!$stillPresent) {
        // we stored timestamps in names; try searching by ts
        if ($rtype === 'class') $marker = 'IntegrationClass-' . $ts;
        if ($rtype === 'subject') $marker = 'INTSUB' . $ts;
        if ($rtype === 'teacher') $marker = 'EMP' . $ts;
        $stillPresent = (strpos($html, $marker) !== false);
    }
    $tests[$rtype]['present_after_unauth'] = $stillPresent;
    echo "  Present after unauthorized attempt: " . ($stillPresent ? 'yes' : 'no') . "\n";

    // Attempt delete as admin (authorized) - expect 302 redirect to index
    $xsrfA = get_xsrf_from_cookie($cookieAdmin);
    $headersA = ['Content-Type: application/x-www-form-urlencoded'];
    if ($xsrfA) $headersA[] = 'X-XSRF-TOKEN: ' . $xsrfA;
    $r3 = curl_exec_req(['url'=>$base . $actionUrl, 'post'=>http_build_query(['_method'=>'DELETE']), 'cookie'=>$cookieAdmin, 'headers'=>$headersA]);
    $adminStatus = $r3['ok'] ? $r3['status'] : 0;
    $tests[$rtype]['admin_status'] = $adminStatus;
    $tests[$rtype]['admin_ok'] = in_array($adminStatus, [200,302,303,204]);
    echo "  Admin delete attempt returned HTTP $adminStatus\n";

    // Verify resource absent now
    $r4 = curl_exec_req(['url'=>$indexUrl, 'cookie'=>$cookieAdmin]);
    $html2 = $r4['ok'] ? $r4['body'] : '';
    $presentAfterAdmin = (strpos($html2, basename($actionUrl)) !== false) || (strpos($html2, htmlspecialchars(basename($actionUrl))) !== false);
    if (!$presentAfterAdmin) {
        if ($rtype === 'class') $marker = 'IntegrationClass-' . $ts;
        if ($rtype === 'subject') $marker = 'INTSUB' . $ts;
        if ($rtype === 'teacher') $marker = 'EMP' . $ts;
        $presentAfterAdmin = (strpos($html2, $marker) !== false);
    }
    $tests[$rtype]['present_after_admin'] = $presentAfterAdmin;
    echo "  Present after admin delete: " . ($presentAfterAdmin ? 'yes' : 'no') . "\n";
}

// Summarize results
$allPassed = true;
foreach ($tests as $rtype => $t) {
    $passUnauth = $t['unauth_teacher_ok'];
    $passAdmin = $t['admin_ok'] && !$t['present_after_admin'];
    $passPreserve = $t['present_after_unauth'];
    $ok = $passUnauth && $passAdmin && $passPreserve;
    echo "\nResource $rtype: unauthorized-blocked=" . ($passUnauth? 'PASS':'FAIL') . ", unauthorized-preserved=" . ($passPreserve? 'PASS':'FAIL') . ", admin-delete=" . ($passAdmin? 'PASS':'FAIL') . "\n";
    if (!$ok) $allPassed = false;
}

// Re-run full integration scripts
echo "\nRe-running full integration audits (sanctum + full audit)\n";
passthru('php scripts\integration_full_audit.php', $exit1);
passthru('php scripts\integration_sanctum_flow.php', $exit2);

echo "\nExit codes: full_audit=$exit1, sanctum_flow=$exit2\n";

if (!$allPassed || $exit1 !== 0 || $exit2 !== 0) {
    echo "\nOverall result: FAIL\n";
    exit(5);
}

echo "\nOverall result: PASS\n";
exit(0);
