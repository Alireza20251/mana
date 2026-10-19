<?php

declare(strict_types=1);

namespace Mana\Core;

final class Response
{
    private int $statusCode = 200;

    private array $headers = [];

    private string $content = '';

    public function __construct(
        string $content = '',
        int $statusCode = 200,
        array $headers = []
    ) {
        $this->content = $content;
        $this->setStatusCode($statusCode);

        foreach ($headers as $name => $value) {
            $this->setHeader((string) $name, (string) $value);
        }
    }

    public function setStatusCode(int $statusCode): self
    {
        if ($statusCode < 100 || $statusCode > 599) {
            throw new \InvalidArgumentException(
                'Invalid HTTP status code.'
            );
        }

        $this->statusCode = $statusCode;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        if (
            preg_match('/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/', $name) !== 1
            || preg_match('/[\r\n]/', $value) === 1
        ) {
            throw new \InvalidArgumentException(
                'Invalid HTTP header.'
            );
        }

        $this->headers[$name] = $value;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function send(): void
    {
        if (headers_sent()) {
            throw new \RuntimeException(
                'Cannot send response: headers already sent.'
            );
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value, true);
        }

        echo $this->content;
    }

    public function json(
        array $data,
        int $statusCode = 200
    ): self {
        $json = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
        );

        $this->setStatusCode($statusCode);
        $this->setHeader(
            'Content-Type',
            'application/json; charset=UTF-8'
        );
        $this->setContent($json);

        return $this;
    }

    public function redirect(
        string $url,
        int $statusCode = 302
    ): self {
        if (!in_array($statusCode, [301, 302, 303, 307, 308], true)) {
            throw new \InvalidArgumentException(
                'Invalid redirect status code.'
            );
        }

        if (
            $url === ''
            || preg_match('/[\r\n]/', $url) === 1
        ) {
            throw new \InvalidArgumentException(
                'Invalid redirect URL.'
            );
        }

        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        $this->setContent('');

        return $this;
    }

    public static function html(
        string $content,
        int $statusCode = 200
    ): self {
        return new self(
            $content,
            $statusCode,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }

    public static function text(
        string $content,
        int $statusCode = 200
    ): self {
        return new self(
            $content,
            $statusCode,
            ['Content-Type' => 'text/plain; charset=UTF-8']
        );
    }
}
