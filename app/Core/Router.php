<?php

declare(strict_types=1);

namespace Mana\Core;

final class Router
{
    private array $routes = [];

    public function __construct(
        private readonly Request $request
    ) {
    }

    public function get(string $path, callable|array $handler): self
    {
        return $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): self
    {
        return $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): self
    {
        return $this->add('PUT', $path, $handler);
    }

    public function patch(string $path, callable|array $handler): self
    {
        return $this->add('PATCH', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): self
    {
        return $this->add('DELETE', $path, $handler);
    }

    public function add(
        string $method,
        string $path,
        callable|array $handler
    ): self {
        $method = strtoupper($method);
        $path = $this->normalizePath($path);

        if (preg_match('/^[A-Z]+$/', $method) !== 1) {
            throw new \InvalidArgumentException(
                'Invalid HTTP method.'
            );
        }

        $this->routes[$method][$path] = $handler;

        return $this;
    }

    public function dispatch(): void
    {
        $method = $this->request->method();
        $path = $this->normalizePath($this->request->path());

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            Response::text('404 - Page Not Found', 404)->send();
            return;
        }

        try {
            $result = $this->resolveHandler($handler);

            if ($result instanceof Response) {
                $result->send();
                return;
            }

            if (is_string($result)) {
                Response::html($result)->send();
                return;
            }

            if ($result === null) {
                return;
            }

            throw new \UnexpectedValueException(
                'Route handler must return a Response, string, or null.'
            );
        } catch (\Throwable $exception) {
            error_log((string) $exception);

            Response::text(
                '500 - Internal Server Error',
                500
            )->send();
        }
    }

    private function resolveHandler(callable|array $handler): mixed
    {
        if (is_array($handler)) {
            if (
                count($handler) !== 2
                || !is_string($handler[0])
                || !is_string($handler[1])
            ) {
                throw new \InvalidArgumentException(
                    'Controller handler must be [ControllerClass, method].'
                );
            }

            [$controllerClass, $method] = $handler;

            if (!class_exists($controllerClass)) {
                throw new \RuntimeException(
                    'Controller class not found: ' . $controllerClass
                );
            }

            $controller = new $controllerClass();

            if (!is_callable([$controller, $method])) {
                throw new \RuntimeException(
                    'Controller method is not callable.'
                );
            }

            return $controller->$method($this->request);
        }

        return $handler($this->request);
    }

    private function normalizePath(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH);

        if (!is_string($path) || $path === '') {
            return '/';
        }

        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : $path;
    }
}
