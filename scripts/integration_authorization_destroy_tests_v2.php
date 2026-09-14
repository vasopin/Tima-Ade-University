<?php
// V2: targeted authorization tests using more robust regex matching (dotall)
$base = 'http://localhost:8000';
$cookieAdmin = __DIR__ . '/.cookie_admin2.txt';
$cookieTeacher = __DIR__ . '/.cookie_teacher2.txt';
$cookieStudent = __DIR__ . '/.cookie_student2.txt';
@unlink($cookieAdmin); @unlink($cookieTeacher); @unlink($cookieStudent);

function req($url, $cookie=null, $post=null, $headers=[]) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    if ($cookie) { curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie); curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); }
    if ($post !== null) { curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, $post); }
    if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $res = curl_exec($ch);
    if ($res === false) { $err = curl_error($ch); curl_close($ch); return ['ok'=>false,'error'=>$err]; }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hlen = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($res,0,$hlen); $body = substr($res,$hlen);
    curl_close($ch);
    return ['ok'=>true,'status'=>$code,'header'=>$header,'body'=>$body];
}

function get_xsrf($cookie) {
    if (!file_exists($cookie)) return null;
    $lines = file($cookie, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $ln) {
        if ($ln[0] === '#') continue;
        $p = preg_split('/\s+/', $ln);
        if (count($p) >= 7 && $p[5] === 'XSRF-TOKEN') return urldecode($p[6]);
    }
    return null;
}

function login($base, $cookie, $email, $pass) {
    $r = req($base . '/sanctum/csrf-cookie', $cookie);
    if (!$r['ok']) return [false, 'csrf_fail'];
    $xs = get_xsrf($cookie);
    $headers = ['Content-Type: application/x-www-form-urlencoded']; if ($xs) $headers[] = 'X-XSRF-TOKEN: ' . $xs;
    $r2 = req($base . '/login', $cookie, http_build_query(['email'=>$email,'password'=>$pass]), $headers);
    if (!$r2['ok']) return [false,'login_err'];
    if (!in_array($r2['status'], [200,302])) return [false,'login_status_'.$r2['status']];
    return [true,'ok'];
}

function create_class($base, $cookie, $name) {
    $xs = get_xsrf($cookie); $headers = ['Content-Type: application/x-www-form-urlencoded']; if ($xs) $headers[] = 'X-XSRF-TOKEN: '.$xs;
    $post = http_build_query(['name'=>$name,'grade_level'=>'10','sections[]'=>'A']);
    $r = req($base . '/classes', $cookie, $post, $headers);
    return $r;
}

function extract_id_by_name($html, $name, $type) {
    // type: classes|subjects|teachers
    $nameQuoted = preg_quote($name, '/');
    $pattern = '/'. $nameQuoted .'.{0,5000}?action="[^"]*\/' . $type . '\/(\d+)"/s';
    if (preg_match($pattern, $html, $m)) return $m[1];
    // try using href for view details
    $pattern2 = '/'. $nameQuoted .'.{0,5000}?href="[^"]*\/' . $type . '\/(\d+)"/s';
    if (preg_match($pattern2, $html, $m2)) return $m2[1];
    return null;
}

// login users
list($ok,$m) = login($base, $cookieAdmin, 'admin@school.com', 'password'); if (!$ok) { echo "admin login failed $m\n"; exit(2); }
// Refresh CSRF cookie to sync session
req($base . '/sanctum/csrf-cookie', $cookieAdmin);
list($ok,$m) = login($base, $cookieTeacher, 'teacher1@school.com', 'password'); if (!$ok) { echo "teacher login failed $m\n"; exit(2); }
req($base . '/sanctum/csrf-cookie', $cookieTeacher);
list($ok,$m) = login($base, $cookieStudent, 'student1@school.com', 'password'); if (!$ok) { echo "student login failed $m\n"; exit(2); }
req($base . '/sanctum/csrf-cookie', $cookieStudent);

$ts = time();
$className = 'IntegrationClassV2-' . $ts;
$subjectName = 'IntegrationSubjectV2-' . $ts; $subjectCode = 'ISUBV2' . $ts;
$teacherName = 'IntegrationTeacherV2 ' . $ts; $teacherEmp = 'EMPV2' . $ts; $teacherEmail = 'integ_teacher_v2_' . $ts . '@example.com';

// create class
$r = create_class($base, $cookieAdmin, $className);
if (!$r['ok'] || !in_array($r['status'], [200,302])) { echo "create class failed status={$r['status']}\n"; exit(3);} 
// get index
$idx = req($base . '/classes', $cookieAdmin);
$html = $idx['body'];
$classId = extract_id_by_name($html, $className, 'classes');
if (!$classId) { file_put_contents(__DIR__.'/dump_classes.html',$html); echo "Could not find class id; dumped to scripts/dump_classes.html\n"; exit(4); }
$actionPathClass = '/classes/' . $classId;
echo "Class created id=$classId action=$actionPathClass\n";

// create subject
$xs = get_xsrf($cookieAdmin); $h = ['Content-Type: application/x-www-form-urlencoded']; if ($xs) $h[]='X-XSRF-TOKEN: '.$xs;
$r = req($base . '/subjects', $cookieAdmin, http_build_query(['name'=>$subjectName,'code'=>$subjectCode]), $h);
if (!$r['ok'] || !in_array($r['status'], [200,302])) { echo "create subject failed\n"; exit(5); }
$idxs = req($base . '/subjects', $cookieAdmin); $subId = extract_id_by_name($idxs['body'],$subjectCode,'subjects');
if (!$subId) { file_put_contents(__DIR__.'/dump_subjects.html',$idxs['body']); echo "Could not find subject id; dumped\n"; exit(6);} $actionPathSubject = '/subjects/' . $subId; echo "Subject id=$subId\n";

// create teacher
$r = req($base . '/teachers', $cookieAdmin, http_build_query(['name'=>$teacherName,'email'=>$teacherEmail,'password'=>'password','employee_id'=>$teacherEmp]), $h);
if (!$r['ok'] || !in_array($r['status'], [200,302])) { echo "create teacher failed status={$r['status']}\n"; exit(7);} $idxt = req($base . '/teachers', $cookieAdmin); $teachId = extract_id_by_name($idxt['body'],$teacherEmp,'teachers'); if (!$teachId) { file_put_contents(__DIR__.'/dump_teachers.html',$idxt['body']); echo "Could not find teacher id; dumped\n"; exit(8);} $actionPathTeacher = '/teachers/'.$teachId; echo "Teacher id=$teachId\n";

// Create a student referencing the class and a section
$createStudentPage = req($base . '/students/create', $cookieAdmin);
if (!$createStudentPage['ok']) { echo "Failed to get students.create page\n"; exit(9); }
$bodyCreate = $createStudentPage['body'];
// find first option value for school_class_id and section_id
if (preg_match('/<select[^>]*name="school_class_id"[^>]*>.*?<option value="(\d+)"/s', $bodyCreate, $m)) {
    $class_for_student = $m[1];
} else { echo "Could not find school_class_id option\n"; exit(10); }
if (preg_match('/<select[^>]*name="section_id"[^>]*>.*?<option value="(\d+)"/s', $bodyCreate, $m2)) {
    $section_for_student = $m2[1];
} else { echo "Could not find section_id option\n"; exit(11); }

$studentEmail = 'integ_student_v2_' . $ts . '@example.com';
$studentRoll = 'R' . $ts; $studentAdmission = 'A' . $ts;
$studentPost = http_build_query([
    'name' => 'IntegrationStudentV2 ' . $ts,
    'email' => $studentEmail,
    'password' => 'password',
    'phone' => '+1000000000',
    'roll_number' => $studentRoll,
    'admission_number' => $studentAdmission,
    'school_class_id' => $class_for_student,
    'section_id' => $section_for_student,
    'admission_date' => date('Y-m-d'),
    'status' => 'active'
]);
$rstudent = req($base . '/students', $cookieAdmin, $studentPost, $h);
if (!$rstudent['ok'] || !in_array($rstudent['status'], [200,302])) { echo "create student failed status={$rstudent['status']}\n"; exit(12); }
// find student id from students index
$idxs2 = req($base . '/students', $cookieAdmin);
if (!$idxs2['ok']) { echo "students index get failed\n"; exit(13); }
if (!preg_match('/action="[^"]*\/students\/(\d+)".*?' . preg_quote($studentAdmission,'/') . '/s', $idxs2['body'], $ms)) {
    // try searching by roll
    if (!preg_match('/action="[^"]*\/students\/(\d+)".*?' . preg_quote($studentRoll,'/') . '/s', $idxs2['body'], $ms)) {
        file_put_contents(__DIR__.'/dump_students.html',$idxs2['body']); echo "Could not find student id; dumped\n"; exit(14);
    }
}
$studentId = $ms[1]; $actionPathStudent = '/students/' . $studentId; echo "Student id=$studentId\n";

$resources = [ 'class'=>$actionPathClass, 'subject'=>$actionPathSubject, 'teacher'=>$actionPathTeacher ];

$allOk = true; $results = [];
foreach ($resources as $rtype => $path) {
    echo "\nTesting $rtype $path\n";
    // attempt delete as teacher (unauth expected 403)
    $xs = get_xsrf($cookieTeacher); $hdr = ['Content-Type: application/x-www-form-urlencoded']; if ($xs) $hdr[]='X-XSRF-TOKEN: '.$xs;
    $r = req($base . $path, $cookieTeacher, http_build_query(['_method'=>'DELETE']), $hdr);
    $status = $r['ok'] ? $r['status'] : 0; echo "  teacher delete status=$status\n";
    $unauthBlocked = ($status === 403);
    // check still present
    $idx = req($base . '/' . ($rtype==='class'?'classes':($rtype==='subject'?'subjects':'teachers')), $cookieTeacher);
    $present = strpos($idx['body'], $ts) !== false; echo "  present after unauth? " . ($present? 'yes':'no') . "\n";
    // attempt admin delete
    $xs = get_xsrf($cookieAdmin); $hdrA=['Content-Type: application/x-www-form-urlencoded']; if ($xs) $hdrA[]='X-XSRF-TOKEN: '.$xs;
    $r2 = req($base . $path, $cookieAdmin, http_build_query(['_method'=>'DELETE']), $hdrA);
    $statusA = $r2['ok'] ? $r2['status'] : 0; echo "  admin delete status=$statusA\n";
    $adminOk = in_array($statusA, [200,204,302,303]);
    $idx2 = req($base . '/' . ($rtype==='class'?'classes':($rtype==='subject'?'subjects':'teachers')), $cookieAdmin);
    $presentAfter = strpos($idx2['body'], $ts) !== false; echo "  present after admin? " . ($presentAfter? 'yes':'no') . "\n";
    $pass = $unauthBlocked && $present && $adminOk && !$presentAfter;
    $results[$rtype]=['unauth_status'=>$status,'unauth_blocked'=>$unauthBlocked,'present_after_unauth'=>$present,'admin_status'=>$statusA,'admin_ok'=>$adminOk,'present_after_admin'=>$presentAfter,'pass'=>$pass];
    if (!$pass) $allOk=false;
}

// rerun integration scripts
echo "\nRerunning integration_full_audit.php and integration_sanctum_flow.php\n";
passthru('php scripts\\integration_full_audit.php', $e1);
passthru('php scripts\\integration_sanctum_flow.php', $e2);
echo "Full audit exit=$e1 sanctum exit=$e2\n";

foreach ($results as $k=>$v) {
    echo "$k: ".($v['pass']? 'PASS':'FAIL')." details: " . json_encode($v) . "\n";
}

if (!$allOk || $e1 !== 0 || $e2 !== 0) { echo "Overall FAIL\n"; exit(9); }
echo "Overall PASS\n"; exit(0);
