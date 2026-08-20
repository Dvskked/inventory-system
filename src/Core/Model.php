<?php

declare(strict_types=1);

namespace InventoryFlow\Core;

/**
 * Base Model class
 */
abstract class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find record by ID
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Find record by column
     */
    public function findBy(string $column, mixed $value): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetchOne($sql, [$value]);
    }

    /**
     * Find all records with optional conditions
     */
    public function all(array $conditions = [], string $orderBy = 'created_at DESC'): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY {$orderBy}";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Create new record
     */
    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->db->query($sql, array_values($data));

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update record by ID
     */
    public function update(int $id, array $data): bool
    {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = ?";
        $params = array_merge(array_values($data), [$id]);

        $this->db->query($sql, $params);
        return true;
    }

    /**
     * Delete record by ID
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $this->db->query($sql, [$id]);
        return true;
    }

    /**
     * Count records
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $result = $this->db->fetchOne($sql, $params);
        return (int) ($result['total'] ?? 0);
    }

    /**
     * Check if record exists
     */
    public function exists(array $conditions): bool
    {
        return $this->count($conditions) > 0;
    }

    /**
     * Search records by term
     */
    public function search(string $term, array $columns): array
    {
        $where = [];
        $params = [];

        foreach ($columns as $column) {
            $where[] = "{$column} LIKE ?";
            $params[] = "%{$term}%";
        }

        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' OR ', $where);
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get table name
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Begin database transaction
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->db->rollback();
    }

    /**
     * Execute raw query
     */
    protected function rawQuery(string $sql, array $params = []): \PDOStatement
    {
        return $this->db->query($sql, $params);
    }

    /**
     * Fetch one from raw query
     */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Fetch all from raw query
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }
}
