<?php
$path = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $path);
$stmt = $pdo->query('SELECT * FROM migrations ORDER BY id');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['id'] . " | " . $r['migration'] . " | " . $r['batch'] . "\n";
}
