<?php

declare(strict_types=1);

namespace InventoryFlow\Core;

/**
 * Base Controller class
 */
abstract class Controller
{
    /**
     * Render a view with data
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../../views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View {$view} not found");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require __DIR__ . '/../../views/layout/main.php';
    }

    /**
     * Render partial view (without layout)
     */
    protected function partial(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../../views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Partial {$view} not found");
        }

        require $viewPath;
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Set JSON response
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Set flash message in session
     */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Get and clear flash message
     */
    protected function getFlash(string $type): ?string
    {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }

        return null;
    }

    /**
     * Get request input
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Check if request is POST
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Get all input data
     */
    protected function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Require authentication
     */
    protected function requireAuth(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            $this->setFlash('error', 'Debes iniciar sesion para acceder');
            $this->redirect('/login');
        }
    }

    /**
     * Get authenticated user ID
     */
    protected function userId(): int
    {
        return (int) $_SESSION['user_id'];
    }

    /**
     * Get user role
     */
    protected function userRole(): string
    {
        return $_SESSION['user_role'] ?? 'employee';
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin(): bool
    {
        return $this->userRole() === 'admin';
    }

    /**
     * Sanitize input string
     */
    protected function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate required fields
     */
    protected function validateRequired(array $fields, array $data): array
    {
        $errors = [];

        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "El campo {$field} es requerido";
            }
        }

        return $errors;
    }

    /**
     * Paginate results
     */
    protected function paginate(string $query, array $params, int $perPage = 15): array
    {
        $page = max(1, (int) ($this->input('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        // Get total count
        $countQuery = preg_replace('/SELECT .+ FROM/', 'SELECT COUNT(*) as total FROM', $query);
        $countQuery = preg_replace('/ORDER BY .+/', '', $countQuery);

        $db = Database::getInstance();
        $total = (int) $db->fetchOne($countQuery, $params)['total'];

        // Get paginated results
        $query .= " LIMIT {$perPage} OFFSET {$offset}";
        $results = $db->fetchAll($query, $params);

        $totalPages = (int) ceil($total / $perPage);

        return [
            'data'        => $results,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
            'has_prev'    => $page > 1,
            'has_next'    => $page < $totalPages,
        ];
    }
}
