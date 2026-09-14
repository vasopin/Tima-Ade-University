<?php
$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) {
    echo "NO_DB_FILE\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $path);
    $stmt = $pdo->query("SELECT name, type, sql FROM sqlite_master WHERE type IN ('table','view') ORDER BY name");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo $r['type'] . ": " . $r['name'] . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
