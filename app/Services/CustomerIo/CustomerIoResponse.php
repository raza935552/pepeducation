<?php

namespace App\Services\CustomerIo;

use Illuminate\Http\Client\Response;

class CustomerIoResponse
{
    protected ?Response $response;
    protected ?string $errorMessage;
    protected bool $isError;

    public function __construct(?Response $response = null)
    {
        $this->response = $response;
        $this->isError = false;
        $this->errorMessage = null;

        if ($response && !$response->successful()) {
            $this->isError = true;
            $this->errorMessage = $this->extractError();
        }
    }

    public static function error(string $message): self
    {
        $instance = new self(null);
        $instance->isError = true;
        $instance->errorMessage = $message;
        return $instance;
    }

    public function isSuccess(): bool { return !$this->isError; }
    public function failed(): bool { return $this->isError; }
    public function getData(): array { return $this->response?->json() ?? []; }
    public function get(string $key, mixed $default = null): mixed { return data_get($this->getData(), $key, $default); }
    public function getError(): ?string { return $this->errorMessage; }
    public function getStatusCode(): ?int { return $this->response?->status(); }
    public function getResponse(): ?Response { return $this->response; }

    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess(),
            'status' => $this->getStatusCode(),
            'error' => $this->getError(),
            'data' => $this->getData(),
        ];
    }

    protected function extractError(): ?string
    {
        if (!$this->response) {
            return $this->errorMessage;
        }

        $body = $this->response->json();
        $status = $this->response->status();

        if (is_array($body)) {
            if (!empty($body['meta']['error'])) {
                return $body['meta']['error'];
            }
            if (!empty($body['errors'])) {
                return is_array($body['errors']) ? implode(', ', $body['errors']) : $body['errors'];
            }
        }

        return match ($status) {
            400 => 'Bad request — check the request body and parameters',
            401 => 'Invalid credentials — check your Site ID and API Key',
            404 => 'Resource not found',
            408 => 'Request timeout',
            429 => 'Rate limited — too many requests',
            default => "HTTP {$status} error",
        };
    }
}
