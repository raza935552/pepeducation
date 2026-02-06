<?php

namespace App\Services\Klaviyo;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KlaviyoClient
{
    protected string $baseUrl = 'https://a.klaviyo.com/api';
    protected string $revision = '2024-02-15';
    protected ?string $privateKey;
    protected ?string $publicKey;
    protected int $maxRetries = 2;

    public function __construct()
    {
        $this->privateKey = Setting::getKlaviyoPrivateKey();
        $this->publicKey = Setting::getKlaviyoPublicKey();
    }

    public function isEnabled(): bool
    {
        return Setting::isKlaviyoEnabled() && $this->privateKey;
    }

    public function post(string $endpoint, array $data): ?array
    {
        return $this->request('post', $endpoint, $data);
    }

    public function get(string $endpoint, array $params = []): ?array
    {
        return $this->request('get', $endpoint, $params);
    }

    public function patch(string $endpoint, array $data): ?array
    {
        return $this->request('patch', $endpoint, $data);
    }

    protected function request(string $method, string $endpoint, array $data): ?array
    {
        if (!$this->isEnabled()) {
            Log::warning('Klaviyo not enabled, skipping API call', ['endpoint' => $endpoint]);
            return null;
        }

        $attempt = 0;

        while ($attempt <= $this->maxRetries) {
            try {
                $http = Http::withHeaders($this->headers())->timeout(30);
                $response = ($method === 'get')
                    ? $http->get($this->baseUrl . $endpoint, $data)
                    : $http->$method($this->baseUrl . $endpoint, $data);

                if ($response->status() === 429) {
                    $retryAfter = (int) ($response->header('Retry-After') ?? 10);
                    $retryAfter = min($retryAfter, 60);
                    Log::warning('Klaviyo rate limited', [
                        'endpoint' => $endpoint,
                        'retry_after' => $retryAfter,
                        'attempt' => $attempt + 1,
                    ]);
                    if ($attempt < $this->maxRetries) {
                        sleep($retryAfter);
                        $attempt++;
                        continue;
                    }
                    return null;
                }

                if ($response->failed()) {
                    Log::error('Klaviyo API error', [
                        'endpoint' => $endpoint,
                        'status' => $response->status(),
                        'body' => $this->sanitizeLogBody($response->body()),
                    ]);
                    return null;
                }

                return $response->json();
            } catch (\Exception $e) {
                Log::error('Klaviyo API exception', [
                    'endpoint' => $endpoint,
                    'error' => $this->sanitizeLogBody($e->getMessage()),
                ]);
                return null;
            }
        }

        return null;
    }

    protected function sanitizeLogBody(string $body): string
    {
        if ($this->privateKey) {
            $body = str_replace($this->privateKey, '[REDACTED]', $body);
        }
        if ($this->publicKey) {
            $body = str_replace($this->publicKey, '[REDACTED]', $body);
        }
        return $body;
    }

    protected function headers(): array
    {
        return [
            'Authorization' => 'Klaviyo-API-Key ' . $this->privateKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'revision' => $this->revision,
        ];
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }
}
