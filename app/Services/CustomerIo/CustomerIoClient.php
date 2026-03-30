<?php

namespace App\Services\CustomerIo;

use App\Models\CustomerIoSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerIoClient
{
    protected string $siteId;
    protected string $apiKey;
    protected string $baseUrl;
    protected int $timeout;

    public function __construct(?CustomerIoSetting $settings = null)
    {
        if (!$settings) {
            $settings = CustomerIoSetting::current();
        }

        if ($settings) {
            $this->siteId = $settings->getSiteId() ?: '';
            $this->apiKey = $settings->getApiKey() ?: '';
            $this->baseUrl = $settings->getBaseUrl();
        } else {
            $this->siteId = config('customerio.site_id', '');
            $this->apiKey = config('customerio.api_key', '');
            $region = config('customerio.region', 'us');
            $urls = config('customerio.base_urls');
            $this->baseUrl = $urls[$region] ?? $urls['us'];
        }

        $this->timeout = config('customerio.timeout', 30);
    }

    public function put(string $endpoint, array $data = []): CustomerIoResponse
    {
        if (!$this->hasCredentials()) {
            Log::warning('Customer.io: Cannot make PUT request — credentials not configured');
            return CustomerIoResponse::error('Customer.io credentials not configured');
        }

        try {
            $response = Http::withBasicAuth($this->siteId, $this->apiKey)
                ->timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->put($this->baseUrl . ltrim($endpoint, '/'), $data);

            return new CustomerIoResponse($response);
        } catch (\Exception $e) {
            Log::error('Customer.io PUT error: ' . $e->getMessage(), ['endpoint' => $endpoint]);
            return CustomerIoResponse::error('Request failed: ' . $e->getMessage());
        }
    }

    public function post(string $endpoint, array $data = []): CustomerIoResponse
    {
        if (!$this->hasCredentials()) {
            Log::warning('Customer.io: Cannot make POST request — credentials not configured');
            return CustomerIoResponse::error('Customer.io credentials not configured');
        }

        try {
            $response = Http::withBasicAuth($this->siteId, $this->apiKey)
                ->timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post($this->baseUrl . ltrim($endpoint, '/'), $data);

            return new CustomerIoResponse($response);
        } catch (\Exception $e) {
            Log::error('Customer.io POST error: ' . $e->getMessage(), ['endpoint' => $endpoint]);
            return CustomerIoResponse::error('Request failed: ' . $e->getMessage());
        }
    }

    public function delete(string $endpoint): CustomerIoResponse
    {
        if (!$this->hasCredentials()) {
            Log::warning('Customer.io: Cannot make DELETE request — credentials not configured');
            return CustomerIoResponse::error('Customer.io credentials not configured');
        }

        try {
            $response = Http::withBasicAuth($this->siteId, $this->apiKey)
                ->timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->delete($this->baseUrl . ltrim($endpoint, '/'));

            return new CustomerIoResponse($response);
        } catch (\Exception $e) {
            Log::error('Customer.io DELETE error: ' . $e->getMessage(), ['endpoint' => $endpoint]);
            return CustomerIoResponse::error('Request failed: ' . $e->getMessage());
        }
    }

    public function testConnection(): CustomerIoResponse
    {
        $testId = '_cio_test_connection_' . time();

        $response = $this->put("customers/{$testId}", [
            '_test' => true,
            'created_at' => time(),
        ]);

        if ($response->isSuccess()) {
            $this->delete("customers/{$testId}");
        }

        return $response;
    }

    public function hasCredentials(): bool
    {
        return !empty($this->siteId) && !empty($this->apiKey);
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
