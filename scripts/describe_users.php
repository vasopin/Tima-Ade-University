<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=moon_college', 'root', '');
$stmt = $pdo->query('DESCRIBE users');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) echo $r['Field']."\t".$r['Type'].PHP_EOL;
