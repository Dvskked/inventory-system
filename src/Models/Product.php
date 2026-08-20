<?php

declare(strict_types=1);

namespace InventoryFlow\Models;

use InventoryFlow\Core\Model;

/**
 * Product Model
 */
class Product extends Model
{
    protected string $table = 'products';

    /**
     * Get all products with category and supplier info
     */
    public function getAllWithRelations(int $limit = 50, int $offset = 0): array
    {
        return $this->fetchAll(
            "SELECT p.*, c.name as category_name, s.name as supplier_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN suppliers s ON p.supplier_id = s.id 
             ORDER BY p.created_at DESC 
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    /**
     * Get product with relations by ID
     */
    public function getWithRelations(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT p.*, c.name as category_name, s.name as supplier_name, s.contact as supplier_contact 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN suppliers s ON p.supplier_id = s.id 
             WHERE p.id = ?",
            [$id]
        );
    }

    /**
     * Find product by SKU
     */
    public function findBySku(string $sku): ?array
    {
        return $this->findBy('sku', $sku);
    }

    /**
     * Get products by category
     */
    public function getByCategory(int $categoryId): array
    {
        return $this->all(['category_id' => $categoryId]);
    }

    /**
     * Get products by supplier
     */
    public function getBySupplier(int $supplierId): array
    {
        return $this->all(['supplier_id' => $supplierId]);
    }

    /**
     * Get products with low stock
     */
    public function getLowStock(): array
    {
        return $this->fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.stock <= p.min_stock AND p.status = 'active' 
             ORDER BY p.stock ASC"
        );
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStock(): array
    {
        return $this->all(['stock' => 0, 'status' => 'active']);
    }

    /**
     * Update product stock
     */
    public function updateStock(int $id, int $quantity): bool
    {
        $sql = "UPDATE products SET stock = stock + ? WHERE id = ?";
        $this->rawQuery($sql, [$quantity, $id]);
        return true;
    }

    /**
     * Decrease stock (for sales)
     */
    public function decreaseStock(int $id, int $quantity): bool
    {
        $product = $this->find($id);

        if (!$product) {
            return false;
        }

        if ($product['stock'] < $quantity) {
            throw new \RuntimeException("Stock insuficiente para el producto: {$product['name']}");
        }

        return $this->updateStock($id, -$quantity);
    }

    /**
     * Search products
     */
    public function searchProducts(string $term): array
    {
        return $this->fetchAll(
            "SELECT p.*, c.name as category_name, s.name as supplier_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN suppliers s ON p.supplier_id = s.id 
             WHERE p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?
             ORDER BY p.name",
            ["%{$term}%", "%{$term}%", "%{$term}%"]
        );
    }

    /**
     * Get product statistics
     */
    public function getStats(): array
    {
        $stats = $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN stock <= min_stock THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(stock * price) as total_value,
                AVG(price) as avg_price
             FROM products"
        );

        return $stats ?: [];
    }

    /**
     * Get top selling products
     */
    public function getTopSelling(int $limit = 10): array
    {
        return $this->fetchAll(
            "SELECT p.*, SUM(si.quantity) as total_sold,
                    SUM(si.subtotal) as total_revenue
             FROM products p
             JOIN sale_items si ON p.id = si.product_id
             JOIN sales s ON si.sale_id = s.id
             WHERE s.status = 'completed'
             GROUP BY p.id
             ORDER BY total_sold DESC
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Check if SKU exists (excluding given ID)
     */
    public function skuExists(string $sku, int $excludeId = 0): bool
    {
        $sql = "SELECT COUNT(*) as total FROM products WHERE sku = ? AND id != ?";
        $result = $this->fetchOne($sql, [$sku, $excludeId]);
        return (int) ($result['total'] ?? 0) > 0;
    }

    /**
     * Get paginated products with search
     */
    public function getPaginated(int $page, int $perPage, string $search = '', int $categoryId = 0): array
    {
        $where = ["1=1"];
        $params = [];

        if ($search !== '') {
            $where[] = "(p.name LIKE ? OR p.sku LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($categoryId > 0) {
            $where[] = "p.category_id = ?";
            $params[] = $categoryId;
        }

        $whereClause = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) as total FROM products p WHERE {$whereClause}";
        $total = (int) $this->fetchOne($countSql, $params)['total'];

        $offset = ($page - 1) * $perPage;
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE {$whereClause}
                ORDER BY p.created_at DESC 
                LIMIT {$perPage} OFFSET {$offset}";

        $data = $this->fetchAll($sql, $params);

        return [
            'data'        => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }
}
