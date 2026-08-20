<?php

declare(strict_types=1);

namespace InventoryFlow\Models;

use InventoryFlow\Core\Model;

/**
 * Customer Model
 */
class Customer extends Model
{
    protected string $table = 'customers';

    /**
     * Get all customers with purchase stats
     */
    public function getAllWithStats(): array
    {
        return $this->fetchAll(
            "SELECT c.*, 
                    COUNT(s.id) as total_purchases,
                    COALESCE(SUM(s.total), 0) as total_spent,
                    MAX(s.created_at) as last_purchase
             FROM customers c 
             LEFT JOIN sales s ON c.id = s.customer_id AND s.status = 'completed'
             GROUP BY c.id 
             ORDER BY total_spent DESC"
        );
    }

    /**
     * Get customer with stats by ID
     */
    public function getWithStats(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT c.*, 
                    COUNT(s.id) as total_purchases,
                    COALESCE(SUM(s.total), 0) as total_spent,
                    MAX(s.created_at) as last_purchase
             FROM customers c 
             LEFT JOIN sales s ON c.id = s.customer_id AND s.status = 'completed'
             WHERE c.id = ?
             GROUP BY c.id",
            [$id]
        );
    }

    /**
     * Get customer by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    /**
     * Search customers
     */
    public function searchCustomers(string $term): array
    {
        return $this->search($term, ['name', 'email', 'phone', 'rfc']);
    }

    /**
     * Get top customers
     */
    public function getTopCustomers(int $limit = 10): array
    {
        return $this->fetchAll(
            "SELECT c.*, 
                    COUNT(s.id) as total_purchases,
                    COALESCE(SUM(s.total), 0) as total_spent
             FROM customers c 
             JOIN sales s ON c.id = s.customer_id AND s.status = 'completed'
             GROUP BY c.id 
             ORDER BY total_spent DESC 
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get customer statistics
     */
    public function getStats(): array
    {
        $stats = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN rfc IS NOT NULL AND rfc != '' THEN 1 ELSE 0 END) with_rfc,
                (SELECT COUNT(DISTINCT customer_id) FROM sales WHERE customer_id IS NOT NULL AND status = 'completed') as active_buyers
             FROM customers"
        );

        return $stats ?: [];
    }

    /**
     * Find customer by RFC
     */
    public function findByRfc(string $rfc): ?array
    {
        return $this->findBy('rfc', $rfc);
    }

    /**
     * Get recent customers
     */
    public function getRecent(int $limit = 5): array
    {
        return $this->all([], "created_at DESC");
    }
}
