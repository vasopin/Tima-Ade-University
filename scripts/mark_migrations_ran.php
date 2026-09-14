<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=moon_college', 'root', '');
// migrations to mark as ran
$migrations = [
    '2024_01_01_000009_create_notices_and_inquiries_table',
    '2026_08_16_035243_create_exams_table',
    '2026_08_16_035244_create_parents_table',
    '2026_08_16_035245_create_school_settings_table',
    '2026_08_16_035250_create_exam_marks_table'
];
// get current max batch
$max = $pdo->query('SELECT MAX(batch) FROM migrations')->fetchColumn();
if (!$max) $max = 1;
foreach ($migrations as $m) {
    // check if already present
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM migrations WHERE migration = ?');
    $stmt->execute([$m]);
    if ($stmt->fetchColumn() > 0) {
        echo "$m already present\n";
        continue;
    }
    $ins = $pdo->prepare('INSERT INTO migrations (migration, batch) VALUES (?, ?)');
    $ins->execute([$m, $max]);
    echo "Marked $m as ran (batch $max)\n";
}
