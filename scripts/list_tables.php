<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=information_schema', 'root', '');
$stmt = $pdo->prepare('SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA = ?');
$stmt->execute(['moon_college']);
$rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach($rows as $r) echo $r.PHP_EOL;
