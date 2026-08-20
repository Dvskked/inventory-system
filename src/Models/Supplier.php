<?php

declare(strict_types=1);

namespace InventoryFlow\Models;

use InventoryFlow\Core\Model;

/**
 * Supplier Model
 */
class Supplier extends Model
{
    protected string $table = 'suppliers';

    /**
     * Get all suppliers with product count
     */
    public function getAllWithCount(): array
    {
        return $this->fetchAll(
            "SELECT s.*, COUNT(p.id) as product_count 
             FROM suppliers s 
             LEFT JOIN products p ON s.id = p.supplier_id 
             GROUP BY s.id 
             ORDER BY s.name"
        );
    }

    /**
     * Get supplier with product count by ID
     */
    public function getWithCount(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT s.*, COUNT(p.id) as product_count 
             FROM suppliers s 
             LEFT JOIN products p ON s.id = p.supplier_id 
             WHERE s.id = ?
             GROUP BY s.id",
            [$id]
        );
    }

    /**
     * Get active suppliers
     */
    public function getActive(): array
    {
        return $this->all(['status' => 'active'], 'name ASC');
    }

    /**
     * Search suppliers
     */
    public function searchSuppliers(string $term): array
    {
        return $this->search($term, ['name', 'contact', 'email', 'phone']);
    }

    /**
     * Get supplier statistics
     */
    public function getStats(): array
    {
        return [
            'total'   => $this->count(),
            'active'  => $this->count(['status' => 'active']),
            'products' => $this->db->fetchOne(
                "SELECT COUNT(*) as total FROM products WHERE supplier_id IS NOT NULL"
            )['total'] ?? 0,
        ];
    }

    /**
     * Get suppliers by product count
     */
    public function getByProductCount(): array
    {
        return $this->fetchAll(
            "SELECT s.*, COUNT(p.id) as product_count 
             FROM suppliers s 
             LEFT JOIN products p ON s.id = p.supplier_id 
             GROUP BY s.id 
             ORDER BY product_count DESC"
        );
    }

    /**
     * Check if supplier has products
     */
    public function hasProducts(int $id): bool
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM products WHERE supplier_id = ?",
            [$id]
        );

        return (int) ($result['total'] ?? 0) > 0;
    }

    /**
     * Safe delete supplier
     */
    public function safeDelete(int $id): bool
    {
        if ($this->hasProducts($id)) {
            throw new \RuntimeException("No se puede eliminar: el proveedor tiene productos asociados");
        }

        return $this->delete($id);
    }
}
