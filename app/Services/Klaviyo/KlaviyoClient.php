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
        if (!$this->isEnabled()) {
            Log::warning('Klaviyo not enabled, skipping API call', ['endpoint' => $endpoint]);
            return null;
        }

        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(30)
                ->post($this->baseUrl . $endpoint, $data);

            if ($response->failed()) {
                Log::error('Klaviyo API error', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Klaviyo API exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function get(string $endpoint, array $params = []): ?array
    {
        if (!$this->isEnabled()) return null;

        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(30)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->failed()) {
                Log::error('Klaviyo API error', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Klaviyo API exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function patch(string $endpoint, array $data): ?array
    {
        if (!$this->isEnabled()) return null;

        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(30)
                ->patch($this->baseUrl . $endpoint, $data);

            if ($response->failed()) {
                Log::error('Klaviyo API error', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Klaviyo API exception', ['error' => $e->getMessage()]);
            return null;
        }
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
