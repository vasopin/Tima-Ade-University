<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=moon_college', 'root', '');
$users = [
    ['email' => 'superadmin_test@local', 'name' => 'Super Admin Test', 'role_id' => 1],
    ['email' => 'universityadmin_test@local', 'name' => 'University Admin Test', 'role_id' => 1],
    ['email' => 'student_test@local', 'name' => 'Student Test', 'role_id' => 3],
    ['email' => 'parent_test@local', 'name' => 'Parent Test', 'role_id' => 4],
];
foreach ($users as $u) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute([$u['email']]);
    if ($stmt->fetchColumn() > 0) {
        echo "User {$u['email']} exists\n";
        continue;
    }
    $hash = password_hash('secret123', PASSWORD_BCRYPT);
    $ins = $pdo->prepare('INSERT INTO users (role_id, name, email, password, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
    $ins->execute([$u['role_id'], $u['name'], $u['email'], $hash, 1]);
    echo "Inserted {$u['email']}\n";
}
