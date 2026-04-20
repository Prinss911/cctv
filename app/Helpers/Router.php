<?php

namespace App\Helpers;

class Router
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = '/' . trim($uri, '/');
        if ($uri !== '/') $uri = rtrim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $params = $this->match($route['path'], $uri);
            if ($params !== false) {
                $this->call($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
    }

    private function match(string $routePath, string $uri): array|false
    {
        if ($routePath === '/' && $uri === '/') {
            return [];
        }

        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $params = [];
        foreach ($routeParts as $i => $part) {
            if (preg_match('/^\{(\w+)\}$/', $part, $matches)) {
                $params[$matches[1]] = $uriParts[$i];
            } elseif ($part !== $uriParts[$i]) {
                return false;
            }
        }

        return $params;
    }

    private function call(string $handler, array $params): void
    {
        [$controllerName, $method] = explode('@', $handler);

        $controllerClass = "App\\Controllers\\$controllerName";
        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "Controller $controllerClass not found.";
            return;
        }

        $controller = new $controllerClass();
        if (!method_exists($controller, $method)) {
            http_response_code(500);
            echo "Method $method not found in $controllerClass.";
            return;
        }

        call_user_func_array([$controller, $method], $params);
    }
}
