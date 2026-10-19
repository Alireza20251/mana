<?php

declare(strict_types=1);

namespace Mana\Core;

final class Application
{
    private Router $router;

    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim(
            $basePath,
            DIRECTORY_SEPARATOR
        );

        $this->router = new Router(Request::capture());
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function basePath(string $path = ''): string
    {
        if ($path === '') {
            return $this->basePath;
        }

        return $this->basePath
            . DIRECTORY_SEPARATOR
            . ltrim($path, DIRECTORY_SEPARATOR);
    }

    public function run(): void
    {
        try {
            $this->router->dispatch();
        } catch (\Throwable $exception) {
            error_log((string) $exception);

            if (!headers_sent()) {
                Response::text(
                    '500 - Internal Server Error',
                    500
                )->send();

                return;
            }

            echo '500 - Internal Server Error';
        }
    }
}
