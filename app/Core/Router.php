<?php

namespace App\Core;

class Router
{
    private array $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        $methodRoutes = $this->routes[$method] ?? [];

        // Fast path: exact match (covers every route that existed before
        // dynamic segments were added — fully backward compatible).
        if (isset($methodRoutes[$path])) {
            $this->invoke($methodRoutes[$path], []);
            return;
        }

        // Pattern match for routes containing {param} segments, e.g. /admin/applications/{id}
        foreach ($methodRoutes as $routePath => $handler) {
            $params = $this->matchPattern($routePath, $path);
            if ($params !== null) {
                $this->invoke($handler, $params);
                return;
            }
        }

        http_response_code(404);
        require __DIR__ . '/../Views/errors/404.php';
    }

    /**
     * Returns an associative array of matched {param} => value pairs,
     * or null if the route pattern doesn't match this path at all.
     */
    private function matchPattern(string $routePath, string $path): ?array
    {
        if (!str_contains($routePath, '{')) {
            return null;
        }

        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $routePath);

        if (!preg_match('#^' . $regex . '$#', $path, $matches)) {
            return null;
        }

        array_shift($matches);
        preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', $routePath, $names);

        return array_combine($names[1], $matches);
    }

    private function invoke(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $action] = $handler;
            $controller = new $class();
            $controller->$action(...array_values($params));
            return;
        }

        $handler(...array_values($params));
    }
}
