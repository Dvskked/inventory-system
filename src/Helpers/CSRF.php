<?php

declare(strict_types=1);

namespace InventoryFlow\Helpers;

/**
 * CSRF Protection helper
 */
class CSRF
{
    /**
     * Generate CSRF token
     */
    public static function generate(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_time'] = time();

        return $token;
    }

    /**
     * Get current CSRF token
     */
    public static function token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            return self::generate();
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Render hidden input field with CSRF token
     */
    public static function field(): string
    {
        return sprintf(
            '<input type="hidden" name="_token" value="%s">',
            htmlspecialchars(self::token())
        );
    }

    /**
     * Verify CSRF token
     */
    public static function verify(?string $token = null): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($token === null) {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        }

        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        // Check token expiration (2 hours)
        if (time() - ($_SESSION['csrf_time'] ?? 0) > 7200) {
            self::invalidate();
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Invalidate current token
     */
    public static function invalidate(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['csrf_token'], $_SESSION['csrf_time']);
    }

    /**
     * Rotate token after use
     */
    public static function rotate(): string
    {
        self::invalidate();
        return self::generate();
    }
}
