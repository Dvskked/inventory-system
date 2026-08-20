<?php

declare(strict_types=1);

namespace InventoryFlow\Core;

/**
 * Simple MVC Router
 */
class Router
{
    private array $routes = [];
    private array $middleware = [];

    /**
     * Register a GET route
     */
    public function get(string $path, array $handler): self
    {
        $this->addRoute('GET', $path, $handler);
        return $this;
    }

    /**
     * Register a POST route
     */
    public function post(string $path, array $handler): self
    {
        $this->addRoute('POST', $path, $handler);
        return $this;
    }

    /**
     * Register middleware
     */
    public function middleware(string $name, callable $callback): self
    {
        $this->middleware[$name] = $callback;
        return $this;
    }

    /**
     * Add route to collection
     */
    private function addRoute(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    /**
     * Dispatch the current request
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove trailing slash (except root)
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            $params = $this->matchRoute($route['path'], $uri);

            if ($params !== false && $route['method'] === $method) {
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        require __DIR__ . '/../../views/errors/404.php';
    }

    /**
     * Match route pattern against URI
     */
    private function matchRoute(string $pattern, string $uri): false|array
    {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return false;
    }

    /**
     * Call the route handler
     */
    private function callHandler(array $handler, array $params): void
    {
        [$controllerClass, $method] = $handler;

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controller class {$controllerClass} not found");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method {$method} not found in {$controllerClass}");
        }

        call_user_func_array([$controller, $method], $params);
    }

    /**
     * Get all registered routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Generate URL with parameters
     */
    public function url(string $path, array $params = []): string
    {
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', (string) $value, $path);
        }

        return $path;
    }
}
