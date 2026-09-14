<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=moon_college', 'root', '');
$stmt = $pdo->query('SELECT id, name, email, role_id FROM users LIMIT 10');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) echo json_encode($r).PHP_EOL;
