<?php

namespace App\Services\Seo\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeProvider
{
    public const MODELS = [
        'claude-sonnet-4-20250514' => 'Claude Sonnet 4 (Recommended)',
        'claude-haiku-4-20250414' => 'Claude Haiku 4 (Fastest)',
        'claude-opus-4-20250514' => 'Claude Opus 4 (Most Capable)',
    ];

    public function __construct(
        protected string $apiKey,
        protected string $model = 'claude-sonnet-4-20250514',
    ) {}

    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(15)->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => 10,
                'messages' => [['role' => 'user', 'content' => 'Say OK']],
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => "Claude API connected. Model: {$this->model}"];
            }

            $error = $response->json('error.message', 'Unknown error');
            return ['success' => false, 'message' => "Claude API error: {$error}"];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()];
        }
    }

    public function generate(string $prompt, int $maxTokens = 500): ?string
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => $maxTokens,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);

            if (!$response->successful()) {
                Log::error('Claude SEO generation failed', [
                    'status' => $response->status(),
                    'error' => $response->json('error.message', $response->body()),
                    'model' => $this->model,
                ]);
                return null;
            }

            return $response->json('content.0.text', '');
        } catch (\Exception $e) {
            Log::error('Claude SEO generation exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
