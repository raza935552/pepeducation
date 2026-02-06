<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UnsplashController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:100',
            'page' => 'integer|min:1|max:50',
        ]);

        $accessKey = Setting::getValue('integrations', 'unsplash_access_key');

        if (!$accessKey) {
            return response()->json([
                'error' => 'Unsplash API key not configured. Add it in Settings > Integrations.',
            ], 422);
        }

        $response = Http::withHeaders([
            'Authorization' => "Client-ID {$accessKey}",
            'Accept' => 'application/json',
        ])->get('https://api.unsplash.com/search/photos', [
            'query' => $request->input('query'),
            'per_page' => 20,
            'page' => $request->input('page', 1),
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'Unsplash API error'], $response->status());
        }

        $data = $response->json();

        return response()->json([
            'total' => $data['total'] ?? 0,
            'total_pages' => $data['total_pages'] ?? 0,
            'results' => collect($data['results'] ?? [])->map(fn($p) => [
                'id' => $p['id'],
                'thumb' => $p['urls']['thumb'] ?? '',
                'small' => $p['urls']['small'] ?? '',
                'regular' => $p['urls']['regular'] ?? '',
                'alt' => $p['alt_description'] ?? $p['description'] ?? '',
                'author' => $p['user']['name'] ?? 'Unknown',
                'author_url' => $p['user']['links']['html'] ?? '',
                'download_url' => $p['links']['download_location'] ?? '',
            ]),
        ]);
    }

    public function trackDownload(Request $request)
    {
        $request->validate(['download_url' => 'required|url']);

        $url = $request->input('download_url');

        // SSRF prevention: only allow Unsplash API URLs
        if (!str_starts_with($url, 'https://api.unsplash.com/')) {
            return response()->json(['ok' => false], 400);
        }

        $accessKey = Setting::getValue('integrations', 'unsplash_access_key');

        if ($accessKey) {
            Http::withHeaders(['Authorization' => "Client-ID {$accessKey}"])->get($url);
        }

        return response()->json(['ok' => true]);
    }
}
