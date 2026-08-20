<?php

/**
 * PHPUnit Bootstrap
 */

// Start session for tests
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set test environment
putenv('APP_ENV=testing');
