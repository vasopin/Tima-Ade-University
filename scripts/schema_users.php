<?php
$path = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $path);
$stmt = $pdo->query("SELECT sql FROM sqlite_master WHERE name='users'");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) echo $row['sql'] . "\n"; else echo "NO_USERS_TABLE\n";
