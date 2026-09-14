<?php
$path = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$migration = $argv[1] ?? null;
$batch = $argv[2] ?? 1;
if (!$migration) {
    echo "Usage: php insert_migration.php <migration_name> [batch]\n";
    exit(1);
}
$stmt = $pdo->prepare('SELECT COUNT(*) as c FROM migrations WHERE migration = ?');
$stmt->execute([$migration]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row['c'] > 0) {
    echo "Migration already present: $migration\n";
    exit(0);
}
$now = date('Y-m-d H:i:s');
$inst = $pdo->prepare('INSERT INTO migrations (migration, batch) VALUES (?, ?)');
$inst->execute([$migration, (int)$batch]);
echo "Inserted migration: $migration (batch $batch)\n";
