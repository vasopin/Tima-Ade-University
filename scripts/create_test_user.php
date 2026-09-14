<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=moon_college', 'root', '');
$email = 'testteacher@local';
$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetchColumn() > 0) { echo "User exists\n"; exit; }
$hash = password_hash('secret123', PASSWORD_BCRYPT);
$ins = $pdo->prepare('INSERT INTO users (role_id, name, email, password, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
$ins->execute([2, 'Test Teacher', $email, $hash, 1]);
echo "Inserted test user $email with password secret123\n";
