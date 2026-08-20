<?php

declare(strict_types=1);

namespace InventoryFlow\Helpers;

use InventoryFlow\Core\Database;

/**
 * Authentication helper
 */
class Auth
{
    private static Database $db;

    /**
     * Initialize database connection
     */
    public static function init(): void
    {
        self::$db = Database::getInstance();
    }

    /**
     * Attempt to authenticate user
     */
    public static function attempt(string $email, string $password): ?array
    {
        self::init();

        $user = self::$db->fetchOne(
            "SELECT * FROM users WHERE email = ? AND status = 'active'",
            [$email]
        );

        if ($user && password_verify($password, $user['password'])) {
            self::login($user);
            return $user;
        }

        return null;
    }

    /**
     * Login user (set session)
     */
    public static function login(array $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        // Regenerate session ID for security
        session_regenerate_id(true);
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return !empty($_SESSION['user_id']);
    }

    /**
     * Get authenticated user
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        self::init();

        return self::$db->fetchOne(
            "SELECT id, name, email, role, created_at FROM users WHERE id = ?",
            [$_SESSION['user_id']]
        );
    }

    /**
     * Get user ID
     */
    public static function id(): ?int
    {
        return self::check() ? (int) $_SESSION['user_id'] : null;
    }

    /**
     * Get user name
     */
    public static function name(): ?string
    {
        return $_SESSION['user_name'] ?? null;
    }

    /**
     * Get user email
     */
    public static function email(): ?string
    {
        return $_SESSION['user_email'] ?? null;
    }

    /**
     * Get user role
     */
    public static function role(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    /**
     * Hash a password
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Create a new user
     */
    public static function createUser(array $data): int
    {
        self::init();

        $data['password'] = self::hashPassword($data['password']);
        $data['created_at'] = date('Y-m-d H:i:s');

        self::$db->query(
            "INSERT INTO users (name, email, password, role, status, created_at) 
             VALUES (?, ?, ?, ?, 'active', ?)",
            [
                $data['name'],
                $data['email'],
                $data['password'],
                $data['role'] ?? 'employee',
                $data['created_at'],
            ]
        );

        return (int) self::$db->lastInsertId();
    }

    /**
     * Check if email already exists
     */
    public static function emailExists(string $email): bool
    {
        self::init();

        $result = self::$db->fetchOne(
            "SELECT COUNT(*) as total FROM users WHERE email = ?",
            [$email]
        );

        return (int) ($result['total'] ?? 0) > 0;
    }
}
