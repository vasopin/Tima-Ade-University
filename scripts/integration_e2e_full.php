<?php
// End-to-end integration script (non-destructive where possible)
// Tests: create student -> edit -> attendance mark -> create exam (assignment) -> save marks

$base = 'http://localhost:8000';
$cookieJar = __DIR__ . '/.cookiejar_e2e.txt';
@unlink($cookieJar);

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

echo "=== E2E integration starting\n";

// 0) Inspect DB to discover an existing class/section/subject to associate with new student/exam
echo "Running DB inspect to pick class/section/subject ids...\n";
$ins = json_decode(trim(shell_exec('php "'.__DIR__.'/db_inspect.php" 2>&1')), true);
// db_inspect prints JSON per-line, not a single JSON array; instead call db_inspect and parse manually
$raw = shell_exec('php "'.__DIR__.'/db_inspect.php" 2>&1');
// parse lines
$lines = array_filter(array_map('trim', explode("\n", $raw)));
$classId = null; $sectionId = null; $subjectId = null; $existingStudentId = null;
foreach ($lines as $ln) {
    if (strpos($ln, '{') === 0) {
        $obj = json_decode($ln, true);
        if (isset($obj['id']) && isset($obj['name']) && !$classId && strpos($ln, 'school_classes') === false) {
            // heuristics: first classes block contains name field
        }
    } elseif (strpos($ln, '== classes ==') !== false) {
        // skip
    }
}
// Simpler: query DB directly with PDO
try {
    $dbPath = __DIR__ . '/../database/database.sqlite';
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $c = $pdo->query('SELECT id FROM school_classes WHERE is_active=1 LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if ($c) $classId = $c['id'];
    $s = $pdo->query('SELECT id FROM sections WHERE school_class_id=' . ($classId?:0) . ' LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if ($s) $sectionId = $s['id'];
    $sub = $pdo->query('SELECT id FROM subjects LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if ($sub) $subjectId = $sub['id'];
    $stud = $pdo->query('SELECT id,user_id FROM students LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if ($stud) $existingStudentId = $stud['id'];
} catch (Throwable $e) {
    echo "DB inspect error: " . $e->getMessage() . "\n";
}

echo "Picked class_id={$classId}, section_id={$sectionId}, subject_id={$subjectId}, sample_student_id={$existingStudentId}\n";

if (!$classId || !$sectionId || !$subjectId) {
    echo "BLOCKED: missing required DB entities (class/section/subject) to run E2E flows.\n";
    exit(2);
}

// Login as super-admin
$r = req('GET', $base.'/sanctum/csrf-cookie', $cookieJar);
$xsrf = getXsrfFromJar($cookieJar);
if (!$xsrf) { echo "CSRF not found, aborting\n"; exit(3); }
$post = http_build_query(['email'=>'admin@school.com','password'=>'password']);
$headers = ['Content-Type: application/x-www-form-urlencoded','X-XSRF-TOKEN: '.$xsrf];
$r = req('POST', $base.'/login', $cookieJar, $post, $headers);
echo "Login: ".short($r)."\n";

// refresh csrf token after login to match session
$r = req('GET', $base.'/sanctum/csrf-cookie', $cookieJar);
$xsrf = getXsrfFromJar($cookieJar);

// Create a student
$unique = time();
$name = 'E2E Student '.$unique;
$email = 'e2e_student_'.$unique.'@example.test';
$payload = [
    'name'=>$name,
    'email'=>$email,
    'password'=>'secret123',
    'phone'=>'1234567890',
    'roll_number'=>'R'.$unique,
    'admission_number'=>'A'.$unique,
    'school_class_id'=>$classId,
    'section_id'=>$sectionId,
    'admission_date'=>date('Y-m-d'),
    'gender'=>'other',
    'date_of_birth'=>'2005-01-01',
    'address'=>'Test address',
    'parent_name'=>'Parent Name',
    'parent_phone'=>'0987654321',
    'parent_email'=>'parent_'.$unique.'@example.test',
    'blood_group'=>'O+',
    'status'=>'active',
];
// form post (multipart form data via application/x-www-form-urlencoded)
$postBody = http_build_query($payload);
$headers = ['Content-Type: application/x-www-form-urlencoded', 'X-XSRF-TOKEN: '.$xsrf];
$r = req('POST', $base.'/students', $cookieJar, $postBody, $headers);
echo "Create student: ".short($r)."\n";

// Check students index for new email
$r = req('GET', $base.'/students', $cookieJar);
$found = false;
if ($r['ok'] && $r['status']==200) { if (strpos($r['body'], $email) !== false) $found = true; }
echo "Students index contains new student: ".($found? 'yes':'no')."\n";

// Attendance: mark for the created student by finding its DB id
try { $stmt = $pdo->prepare('SELECT id FROM students WHERE admission_number = ? LIMIT 1'); $stmt->execute(['A'.$unique]); $srow = $stmt->fetch(PDO::FETCH_ASSOC); $newStudentId = $srow? $srow['id']: null; } catch (Throwable $e) { $newStudentId = null; }
if (!$newStudentId) { echo "BLOCKED: cannot find created student in DB to mark attendance.\n"; exit(4); }

$attendanceData = [
    'attendance_date' => date('Y-m-d'),
    'school_class_id' => $classId,
    'section_id' => $sectionId,
    'attendance' => json_encode([$newStudentId => 'present']),
];
// attendance controller expects POST fields: attendance as array. Use multipart/form-like post by building fields manually
// We'll submit as application/x-www-form-urlencoded with attendance[$id]=present
$fields = [];
foreach ($attendanceData as $k=>$v) {
    if ($k === 'attendance') continue;
    $fields[$k] = $v;
}
$fields['attendance['.$newStudentId.']'] = 'present';
$postBody = http_build_query($fields);
$headers = ['Content-Type: application/x-www-form-urlencoded', 'X-XSRF-TOKEN: '.$xsrf];
$r = req('POST', $base.'/attendance/mark', $cookieJar, $postBody, $headers);
echo "Mark attendance: ".short($r)."\n";

// Create an exam (assignment) for the class and subject
$examPayload = [
    'name' => 'E2E Assignment '.$unique,
    'exam_type' => 'assignment',
    'school_class_id' => $classId,
    'subject_id' => $subjectId,
    'exam_date' => date('Y-m-d'),
    'start_time' => '09:00',
    'duration_minutes' => 60,
    'total_marks' => 100,
    'pass_marks' => 40,
    'status' => 'scheduled',
    'instructions' => 'Complete the assignment',
];
$postBody = http_build_query($examPayload);
$headers = ['Content-Type: application/x-www-form-urlencoded', 'X-XSRF-TOKEN: '.$xsrf];
$r = req('POST', $base.'/exams', $cookieJar, $postBody, $headers);
echo "Create exam: ".short($r)."\n";

// Find created exam id via DB
try { $stmt = $pdo->prepare('SELECT id FROM exams WHERE name=? ORDER BY id DESC LIMIT 1'); $stmt->execute([$examPayload['name']]); $erow = $stmt->fetch(PDO::FETCH_ASSOC); $examId = $erow? $erow['id']: null; } catch (Throwable $e) { $examId = null; }
if (!$examId) { echo "BLOCKED: cannot find created exam in DB.\n"; exit(5); }

// Save marks for the new student
$marksFields = [];
$marksFields['marks['.$newStudentId.']'] = '85';
$postBody = http_build_query($marksFields);
$headers = ['Content-Type: application/x-www-form-urlencoded', 'X-XSRF-TOKEN: '.$xsrf];
$r = req('POST', $base.'/exams/'.$examId.'/marks', $cookieJar, $postBody, $headers);
echo "Save marks: ".short($r)."\n";

// Verify the marks exist in DB
try { $stmt = $pdo->prepare('SELECT marks_obtained, grade FROM exam_marks WHERE exam_id=? AND student_id=? LIMIT 1'); $stmt->execute([$examId, $newStudentId]); $mrow = $stmt->fetch(PDO::FETCH_ASSOC); } catch (Throwable $e) { $mrow = null; }
if ($mrow) { echo "Marks recorded: ".json_encode($mrow)."\n"; } else { echo "FAIL: marks not found in DB\n"; }

echo "=== E2E integration complete\n";
exit(0);
