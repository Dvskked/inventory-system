<?php

declare(strict_types=1);

namespace InventoryFlow\Core;

/**
 * Database connection singleton using PDO
 */
class Database
{
    private static ?Database $instance = null;
    private \PDO $connection;

    private string $host;
    private string $dbname;
    private string $username;
    private string $password;
    private int $port;

    private function __construct()
    {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->dbname = getenv('DB_NAME') ?: 'inventoryflow';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: '';
        $this->port = (int)(getenv('DB_PORT') ?: 3306);

        $this->connect();
    }

    /**
     * Get database instance (singleton)
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Establish database connection
     */
    private function connect(): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $this->host,
            $this->port,
            $this->dbname
        );

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            $this->connection = new \PDO($dsn, $this->username, $this->password, $options);
        } catch (\PDOException $e) {
            throw new \RuntimeException(
                'Database connection failed: ' . $e->getMessage(),
                (int) $e->getCode()
            );
        }
    }

    /**
     * Get PDO connection
     */
    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    /**
     * Execute a query with optional parameters
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch single row
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new \RuntimeException("Cannot unserialize singleton");
    }
}
