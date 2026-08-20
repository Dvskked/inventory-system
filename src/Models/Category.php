<?php

declare(strict_types=1);

namespace InventoryFlow\Models;

use InventoryFlow\Core\Model;

/**
 * Category Model
 */
class Category extends Model
{
    protected string $table = 'categories';

    /**
     * Get all categories with product count
     */
    public function getAllWithCount(): array
    {
        return $this->fetchAll(
            "SELECT c.*, COUNT(p.id) as product_count 
             FROM categories c 
             LEFT JOIN products p ON c.id = p.category_id 
             GROUP BY c.id 
             ORDER BY c.name"
        );
    }

    /**
     * Get category with product count by ID
     */
    public function getWithCount(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT c.*, COUNT(p.id) as product_count 
             FROM categories c 
             LEFT JOIN products p ON c.id = p.category_id 
             WHERE c.id = ?
             GROUP BY c.id",
            [$id]
        );
    }

    /**
     * Get root categories (no parent)
     */
    public function getRoots(): array
    {
        return $this->all(['parent_id' => null], 'name ASC');
    }

    /**
     * Get child categories
     */
    public function getChildren(int $parentId): array
    {
        return $this->all(['parent_id' => $parentId], 'name ASC');
    }

    /**
     * Get category hierarchy
     */
    public function getHierarchy(): array
    {
        $all = $this->all([], 'name ASC');
        $tree = [];
        $grouped = [];

        foreach ($all as $category) {
            $grouped[$category['parent_id'] ?? 'root'][] = $category;
        }

        $parentId = null;
        if (isset($grouped['root'])) {
            foreach ($grouped['root'] as $root) {
                $root['children'] = $grouped[$root['id']] ?? [];
                $tree[] = $root;
            }
        }

        return $tree;
    }

    /**
     * Get breadcrumb path for category
     */
    public function getBreadcrumb(int $categoryId): array
    {
        $breadcrumb = [];
        $current = $this->find($categoryId);

        while ($current) {
            array_unshift($breadcrumb, $current);

            if (!empty($current['parent_id'])) {
                $current = $this->find((int) $current['parent_id']);
            } else {
                break;
            }
        }

        return $breadcrumb;
    }

    /**
     * Search categories
     */
    public function searchCategories(string $term): array
    {
        return $this->search($term, ['name', 'description']);
    }

    /**
     * Get category statistics
     */
    public function getStats(): array
    {
        return [
            'total'        => $this->count(),
            'with_products' => $this->db->fetchOne(
                "SELECT COUNT(DISTINCT category_id) as total FROM products WHERE category_id IS NOT NULL"
            )['total'] ?? 0,
        ];
    }

    /**
     * Check if category has products
     */
    public function hasProducts(int $id): bool
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM products WHERE category_id = ?",
            [$id]
        );

        return (int) ($result['total'] ?? 0) > 0;
    }

    /**
     * Delete category (only if no products)
     */
    public function safeDelete(int $id): bool
    {
        if ($this->hasProducts($id)) {
            throw new \RuntimeException("No se puede eliminar: la categoria tiene productos asociados");
        }

        // Also check for children
        $children = $this->getChildren($id);
        if (!empty($children)) {
            throw new \RuntimeException("No se puede eliminar: la categoria tiene subcategorias");
        }

        return $this->delete($id);
    }
}
