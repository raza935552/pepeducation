<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiContentController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:headline,paragraph,cta,benefits,testimonial,faq',
            'prompt' => 'required|string|max:1000',
            'tone' => 'required|string|in:professional,casual,persuasive,urgent,friendly',
        ]);

        $apiKey = Setting::get('openai_api_key');
        if (! $apiKey) {
            return response()->json(['error' => 'AI API key not configured. Go to Settings.'], 422);
        }

        $systemPrompt = $this->buildSystemPrompt($validated['type'], $validated['tone']);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => Setting::get('openai_model', 'gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $validated['prompt']],
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                return response()->json(['content' => trim($content)]);
            }

            return response()->json(['error' => 'AI API returned an error.'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to connect to AI service.'], 500);
        }
    }

    private function buildSystemPrompt(string $type, string $tone): string
    {
        $typeInstructions = [
            'headline' => 'Write a compelling marketing headline. Return only the headline text.',
            'paragraph' => 'Write a marketing paragraph (2-3 sentences). Return only the paragraph.',
            'cta' => 'Write a short call-to-action text (3-8 words). Return only the CTA.',
            'benefits' => 'Write 3-5 benefit bullet points. Each on a new line starting with a checkmark.',
            'testimonial' => 'Write a realistic customer testimonial quote (2-3 sentences).',
            'faq' => 'Write a clear FAQ answer (2-3 sentences). Return only the answer.',
        ];

        $instruction = $typeInstructions[$type] ?? $typeInstructions['paragraph'];

        return "You are a marketing copywriter. Tone: {$tone}. {$instruction} Do not include quotes around the text unless it's a testimonial.";
    }
}
