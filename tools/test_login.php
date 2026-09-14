<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $stmt = $pdo->prepare('SELECT `password` FROM `Tima-Ade University`.`users` WHERE `email` = ? LIMIT 1');
    $stmt->execute(['admin@school.com']);
    $hash = $stmt->fetchColumn();
    if (!$hash) { echo "NO_USER\n"; exit(1);}    
    echo (password_verify('password', $hash) ? "PASSWORD_OK\n" : "PASSWORD_FAIL\n");
} catch (PDOException $e) {
    echo "ERR: " . $e->getMessage() . "\n";
    exit(1);
}
