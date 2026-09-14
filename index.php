<?php

/**
 * Tima-Ade University - Single URL Entry Point
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// If request is directly to root index.php, redirect to public directory or load directly
require_once __DIR__ . '/public/index.php';
