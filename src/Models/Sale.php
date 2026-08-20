<?php

declare(strict_types=1);

namespace InventoryFlow\Models;

use InventoryFlow\Core\Model;
use InventoryFlow\Core\Database;

/**
 * Sale Model
 */
class Sale extends Model
{
    protected string $table = 'sales';

    /**
     * Get all sales with customer and user info
     */
    public function getAllWithRelations(int $limit = 50, int $offset = 0): array
    {
        return $this->fetchAll(
            "SELECT s.*, c.name as customer_name, u.name as user_name 
             FROM sales s 
             LEFT JOIN customers c ON s.customer_id = c.id 
             LEFT JOIN users u ON s.user_id = u.id 
             ORDER BY s.created_at DESC 
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    /**
     * Get sale with full details by ID
     */
    public function getWithDetails(int $id): ?array
    {
        $sale = $this->fetchOne(
            "SELECT s.*, c.name as customer_name, c.email as customer_email,
                    u.name as user_name 
             FROM sales s 
             LEFT JOIN customers c ON s.customer_id = c.id 
             LEFT JOIN users u ON s.user_id = u.id 
             WHERE s.id = ?",
            [$id]
        );

        if ($sale) {
            $sale['items'] = $this->getSaleItems($id);
        }

        return $sale;
    }

    /**
     * Get items for a sale
     */
    public function getSaleItems(int $saleId): array
    {
        return $this->fetchAll(
            "SELECT si.*, p.name as product_name, p.sku as product_sku 
             FROM sale_items si 
             JOIN products p ON si.product_id = p.id 
             WHERE si.sale_id = ?
             ORDER BY si.id",
            [$saleId]
        );
    }

    /**
     * Create a sale with items (transaction)
     */
    public function createSale(array $saleData, array $items): int
    {
        $this->beginTransaction();

        try {
            // Create the sale
            $saleId = $this->create($saleData);

            // Create sale items and update stock
            $productModel = new Product();

            foreach ($items as $item) {
                // Create sale item
                $this->rawQuery(
                    "INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) 
                     VALUES (?, ?, ?, ?, ?)",
                    [
                        $saleId,
                        $item['product_id'],
                        $item['quantity'],
                        $item['price'],
                        $item['quantity'] * $item['price'],
                    ]
                );

                // Decrease stock
                $productModel->decreaseStock((int) $item['product_id'], (int) $item['quantity']);
            }

            $this->commit();
            return $saleId;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Get sales by date range
     */
    public function getByDateRange(string $startDate, string $endDate): array
    {
        return $this->fetchAll(
            "SELECT s.*, c.name as customer_name 
             FROM sales s 
             LEFT JOIN customers c ON s.customer_id = c.id 
             WHERE DATE(s.created_at) BETWEEN ? AND ? 
             ORDER BY s.created_at DESC",
            [$startDate, $endDate]
        );
    }

    /**
     * Get daily sales summary
     */
    public function getDailySummary(int $days = 30): array
    {
        return $this->fetchAll(
            "SELECT DATE(created_at) as date, 
                    COUNT(*) as sales_count,
                    SUM(total) as total_sales,
                    AVG(total) as avg_sale
             FROM sales 
             WHERE status = 'completed' 
               AND created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY DATE(created_at) 
             ORDER BY date DESC",
            [$days]
        );
    }

    /**
     * Get monthly sales summary
     */
    public function getMonthlySummary(int $months = 12): array
    {
        return $this->fetchAll(
            "SELECT YEAR(created_at) as year,
                    MONTH(created_at) as month,
                    COUNT(*) as sales_count,
                    SUM(total) as total_sales,
                    AVG(total) as avg_sale
             FROM sales 
             WHERE status = 'completed' 
               AND created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY YEAR(created_at), MONTH(created_at) 
             ORDER BY year DESC, month DESC",
            [$months]
        );
    }

    /**
     * Get sales statistics
     */
    public function getStats(string $startDate = '', string $endDate = ''): array
    {
        $where = "status = 'completed'";
        $params = [];

        if ($startDate !== '') {
            $where .= " AND DATE(created_at) >= ?";
            $params[] = $startDate;
        }

        if ($endDate !== '') {
            $where .= " AND DATE(created_at) <= ?";
            $params[] = $endDate;
        }

        return $this->db->fetchOne(
            "SELECT 
                COUNT(*) as total_sales,
                COALESCE(SUM(total), 0) as total_revenue,
                COALESCE(AVG(total), 0) as avg_sale,
                COALESCE(SUM(discount), 0) as total_discounts,
                COALESCE(SUM(tax), 0) as total_taxes
             FROM sales 
             WHERE {$where}",
            $params
        ) ?: [];
    }

    /**
     * Get sales by user
     */
    public function getByUser(int $userId, int $limit = 10): array
    {
        return $this->all(
            ['user_id' => $userId],
            "created_at DESC"
        );
    }

    /**
     * Get recent sales
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->fetchAll(
            "SELECT s.*, c.name as customer_name, u.name as user_name 
             FROM sales s 
             LEFT JOIN customers c ON s.customer_id = c.id 
             LEFT JOIN users u ON s.user_id = u.id 
             ORDER BY s.created_at DESC 
             LIMIT ?",
            [$limit]
        );
    }

    /**
     * Cancel a sale (restore stock)
     */
    public function cancelSale(int $id): bool
    {
        $this->beginTransaction();

        try {
            $sale = $this->find($id);

            if (!$sale) {
                throw new \RuntimeException("Venta no encontrada");
            }

            if ($sale['status'] === 'cancelled') {
                throw new \RuntimeException("La venta ya esta cancelada");
            }

            // Get sale items
            $items = $this->getSaleItems($id);

            // Restore stock for each item
            $productModel = new Product();
            foreach ($items as $item) {
                $productModel->updateStock((int) $item['product_id'], (int) $item['quantity']);
            }

            // Update sale status
            $this->update($id, ['status' => 'cancelled']);

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Get ticket number
     */
    public function generateTicketNumber(): string
    {
        $date = date('Ymd');
        $count = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM sales WHERE DATE(created_at) = CURDATE()"
        )['total'] ?? 0;

        return sprintf('TKT-%s-%04d', $date, $count + 1);
    }
}
