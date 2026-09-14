<?php
// Simple DB inspector for sqlite database used in tests
$dbPath = __DIR__ . '/../database/database.sqlite';
if (!file_exists($dbPath)) {
    echo "Database not found at $dbPath\n";
    exit(2);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $queries = [
        'classes' => "SELECT id, name FROM school_classes LIMIT 5",
        'sections' => "SELECT id, name, school_class_id FROM sections LIMIT 10",
        'subjects' => "SELECT id, name FROM subjects LIMIT 10",
        'students' => "SELECT id, user_id, roll_number, admission_number FROM students LIMIT 10",
        'users' => "SELECT id, name, email, role_id FROM users LIMIT 10",
    ];

    foreach ($queries as $k => $q) {
        echo "== $k ==\n";
        $stmt = $pdo->query($q);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) { echo "(no rows)\n\n"; continue; }
        foreach ($rows as $r) {
            echo json_encode($r) . "\n";
        }
        echo "\n";
    }

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(3);
}

exit(0);
