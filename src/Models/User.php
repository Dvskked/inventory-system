<?php

declare(strict_types=1);

namespace InventoryFlow\Models;

use InventoryFlow\Core\Model;

/**
 * User Model
 */
class User extends Model
{
    protected string $table = 'users';

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    /**
     * Get all active users
     */
    public function getActive(): array
    {
        return $this->all(['status' => 'active']);
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role): array
    {
        return $this->all(['role' => $role]);
    }

    /**
     * Update user password
     */
    public function updatePassword(int $id, string $hashedPassword): bool
    {
        return $this->update($id, ['password' => $hashedPassword]);
    }

    /**
     * Deactivate user
     */
    public function deactivate(int $id): bool
    {
        return $this->update($id, ['status' => 'inactive']);
    }

    /**
     * Get user statistics
     */
    public function getStats(): array
    {
        return [
            'total'    => $this->count(),
            'active'   => $this->count(['status' => 'active']),
            'admins'   => $this->count(['role' => 'admin']),
            'employees' => $this->count(['role' => 'employee']),
        ];
    }

    /**
     * Search users
     */
    public function searchUsers(string $term): array
    {
        return $this->search($term, ['name', 'email']);
    }

    /**
     * Get user with sales count
     */
    public function getWithSalesCount(): array
    {
        return $this->fetchAll(
            "SELECT u.*, COUNT(s.id) as sales_count 
             FROM users u 
             LEFT JOIN sales s ON u.id = s.user_id 
             GROUP BY u.id 
             ORDER BY sales_count DESC"
        );
    }
}
