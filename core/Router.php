<?php

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path = $this->normalize(parse_url($uri, PHP_URL_PATH) ?: '/');
        $map = $this->routes[$method] ?? [];
        $handler = $map[$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 — rota não encontrada';
            return;
        }

        if (is_array($handler)) {
            [$class, $action] = $handler;
            $ctrl = new $class();
            $ctrl->$action();
            return;
        }

        $handler();
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
