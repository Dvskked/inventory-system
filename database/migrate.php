<?php

/**
 * Database Migration Runner
 * Usage: php database/migrate.php
 */

// Load environment variables if .env exists
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Database configuration
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: 3306;
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'inventoryflow';

echo "========================================\n";
echo "  InventoryFlow - Database Migration\n";
echo "========================================\n\n";

try {
    // Connect without database
    $dsn = "mysql:host={$host};port={$port}";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "[1/3] Creating database if not exists...\n";

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` 
                 CHARACTER SET utf8mb4 
                 COLLATE utf8mb4_unicode_ci");

    echo "      Database '{$database}' ready.\n\n";

    // Select database
    $pdo->exec("USE `{$database}`");

    echo "[2/3] Running migrations...\n\n";

    // Get migration files
    $migrationsDir = __DIR__ . '/migrations';
    $files = glob($migrationsDir . '/*.sql');
    sort($files);

    if (empty($files)) {
        echo "      No migration files found.\n";
    } else {
        foreach ($files as $file) {
            $filename = basename($file);
            echo "  Running: {$filename}\n";

            $sql = file_get_contents($file);
            $pdo->exec($sql);

            echo "  ✓ Success\n\n";
        }
    }

    echo "[3/3] Creating default admin user...\n\n";

    // Create default admin user
    $adminPassword = password_hash('Admin123!', PASSWORD_BCRYPT, ['cost' => 12]);
    $now = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, email, password, role, status, created_at) 
                           VALUES (?, ?, ?, 'admin', 'active', ?)");
    $stmt->execute([
        'Administrador',
        'admin@inventoryflow.com',
        $adminPassword,
        $now,
    ]);

    if ($stmt->rowCount() > 0) {
        echo "      Default admin user created:\n";
        echo "        Email: admin@inventoryflow.com\n";
        echo "        Password: Admin123!\n\n";
    } else {
        echo "      Admin user already exists.\n\n";
    }

    echo "========================================\n";
    echo "  Migration completed successfully!\n";
    echo "========================================\n";

} catch (PDOException $e) {
    echo "\nError: " . $e->getMessage() . "\n\n";
    echo "Make sure MySQL is running and credentials are correct.\n";
    echo "Check your .env file or environment variables.\n";
    exit(1);
}
