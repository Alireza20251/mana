<?php
declare(strict_types=1);

namespace Mana\Core;

final class Request
{
    private array $query;
    private array $post;
    private array $server;
    private array $cookies;
    private array $files;

    public function __construct(
        ?array $query = null,
        ?array $post = null,
        ?array $server = null,
        ?array $cookies = null,
        ?array $files = null
    ) {
        $this->query = $query ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->server = $server ?? $_SERVER;
        $this->cookies = $cookies ?? $_COOKIE;
        $this->files = $files ?? $_FILES;
    }

    public static function capture(): self
    {
        return new self();
    }

    public function method(): string
    {
        return strtoupper(
            (string) ($this->server['REQUEST_METHOD'] ?? 'GET')
        );
    }

    public function path(): string
    {
        $uri = (string) ($this->server['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);

        if (!is_string($path) || $path === '') {
            return '/';
        }

        // حذف مسیر پایه پروژه در محیط محلی
        $basePath = '/mana/public';

        if ($path === $basePath) {
            return '/';
        }

        if (str_starts_with($path, $basePath . '/')) {
            $path = substr($path, strlen($basePath));
        }

        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : $path;
    }

    public function query(
        ?string $key = null,
        mixed $default = null
    ): mixed {
        return $key === null
            ? $this->query
            : ($this->query[$key] ?? $default);
    }

    public function post(
        ?string $key = null,
        mixed $default = null
    ): mixed {
        return $key === null
            ? $this->post
            : ($this->post[$key] ?? $default);
    }

    public function input(
        ?string $key = null,
        mixed $default = null
    ): mixed {
        $data = array_replace_recursive(
            $this->query,
            $this->post
        );

        return $key === null
            ? $data
            : ($data[$key] ?? $default);
    }

    public function only(array $keys): array
    {
        return array_intersect_key(
            $this->input(),
            array_flip($keys)
        );
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->post)
            || array_key_exists($key, $this->query);
    }

    public function header(
        string $name,
        mixed $default = null
    ): mixed {
        $normalized = strtoupper(str_replace('-', '_', $name));
        $key = 'HTTP_' . $normalized;

        if (strcasecmp($name, 'Content-Type') === 0) {
            $key = 'CONTENT_TYPE';
        } elseif (strcasecmp($name, 'Content-Length') === 0) {
            $key = 'CONTENT_LENGTH';
        }

        return $this->server[$key] ?? $default;
    }

    public function bearerToken(): ?string
    {
        $authorization = $this->header('Authorization');

        if (!is_string($authorization)) {
            return null;
        }

        if (
            preg_match(
                '/^Bearer\s+(.+)$/i',
                trim($authorization),
                $matches
            ) !== 1
        ) {
            return null;
        }

        return trim($matches[1]);
    }

    public function cookie(
        string $name,
        mixed $default = null
    ): mixed {
        return $this->cookies[$name] ?? $default;
    }

    public function file(?string $name = null): mixed
    {
        return $name === null
            ? $this->files
            : ($this->files[$name] ?? null);
    }

    public function server(
        string $key,
        mixed $default = null
    ): mixed {
        return $this->server[$key] ?? $default;
    }

    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    public function isAjax(): bool
    {
        return strtolower(
            (string) $this->header('X-Requested-With')
        ) === 'xmlhttprequest';
    }

    public function isSecure(): bool
    {
        return (
            ($this->server['HTTPS'] ?? '') !== ''
            && strtolower(
                (string) $this->server['HTTPS']
            ) !== 'off'
        ) || (int) ($this->server['SERVER_PORT'] ?? 0) === 443;
    }

    public function ip(): ?string
    {
        $ip = $this->server['REMOTE_ADDR'] ?? null;

        return is_string($ip)
            && filter_var($ip, FILTER_VALIDATE_IP)
                ? $ip
                : null;
    }

    public function userAgent(): string
    {
        return (string) (
            $this->server['HTTP_USER_AGENT'] ?? ''
        );
    }
}
