<?php

/**
 * InventoryFlow - Entry Point
 * All requests are routed through this file
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base path
define('BASE_PATH', __DIR__ . '/..');

// Autoloader
require BASE_PATH . '/vendor/autoload.php';

// Load routes
require BASE_PATH . '/routes/web.php';
