<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Peptide;
use App\Models\Setting;
use App\Services\Seo\Providers\ClaudeProvider;
use App\Services\Seo\SeoGeneratorService;
use Illuminate\Http\Request;

class SeoSettingsController extends Controller
{
    public function index()
    {
        $claudeModels = ClaudeProvider::MODELS;
        $claudeApiKey = Setting::getValue('seo', 'claude_api_key');
        $claudeModel = Setting::getValue('seo', 'claude_model', 'claude-sonnet-4-20250514');
        $autoGenerate = Setting::getValue('seo', 'auto_generate_on_save', false);

        $maskedKey = SeoGeneratorService::maskKey($claudeApiKey);
        $hasKey = !empty($claudeApiKey);

        $missingCount = Peptide::published()
            ->where(fn ($q) => $q->whereNull('meta_title')
                ->orWhereNull('meta_description')
                ->orWhere('meta_title', '')
                ->orWhere('meta_description', '')
                ->orWhere('meta_title', 'like', '% - Research, Dosing & Protocols'))
            ->count();

        $totalPeptides = Peptide::published()->count();

        return view('admin.settings.seo', compact(
            'claudeModels', 'claudeModel', 'maskedKey', 'hasKey',
            'autoGenerate', 'missingCount', 'totalPeptides'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'claude_model' => 'nullable|string|max:100',
            'claude_api_key' => 'nullable|string|max:500',
            'auto_generate_on_save' => 'nullable',
        ]);

        if ($request->filled('claude_api_key') && !str_contains($request->claude_api_key, '****')) {
            Setting::setValue('seo', 'claude_api_key', SeoGeneratorService::encryptKey($request->claude_api_key));
            // Store type as encrypted for the Setting model
            $setting = \App\Models\Setting::where('group', 'seo')->where('key', 'claude_api_key')->first();
            if ($setting) {
                $setting->update(['type' => 'encrypted']);
            }
        }

        if ($request->filled('claude_model')) {
            Setting::setValue('seo', 'claude_model', $request->claude_model);
        }

        Setting::setValue('seo', 'auto_generate_on_save', (bool) $request->auto_generate_on_save);

        return back()->with('success', 'SEO AI settings saved.');
    }

    public function testConnection(Request $request)
    {
        $request->validate(['api_key' => 'required|string|min:10']);

        $generator = new SeoGeneratorService();
        $provider = $generator->getProviderWithKey($request->api_key);

        return response()->json($provider->testConnection());
    }

    public function pendingPeptides(Request $request)
    {
        $query = Peptide::published();

        if (!$request->has('all')) {
            $query->where(fn ($q) => $q->whereNull('meta_title')
                ->orWhereNull('meta_description')
                ->orWhere('meta_title', '')
                ->orWhere('meta_description', '')
                ->orWhere('meta_title', 'like', '% - Research, Dosing & Protocols'));
        }

        $peptides = $query->select('id', 'name')->orderBy('name')->get();

        return response()->json(['items' => $peptides]);
    }

    public function generateOne(Request $request)
    {
        $request->validate(['peptide_id' => 'required|exists:peptides,id']);

        $peptide = Peptide::with('categories')->findOrFail($request->peptide_id);
        $generator = new SeoGeneratorService();

        if (!$generator->isConfigured()) {
            return response()->json(['success' => false, 'error' => 'Claude API key not configured.']);
        }

        $result = $generator->generateForPeptide($peptide);

        if (!$result) {
            return response()->json([
                'success' => false,
                'peptide_id' => $peptide->id,
                'name' => $peptide->name,
                'error' => 'AI generation failed. Check API key and try again.',
            ]);
        }

        $peptide->update($result);

        return response()->json([
            'success' => true,
            'peptide_id' => $peptide->id,
            'name' => $peptide->name,
            'meta_title' => $result['meta_title'],
            'meta_description' => $result['meta_description'],
        ]);
    }

    public function rewriteOverview(Request $request)
    {
        $request->validate(['peptide_id' => 'required|exists:peptides,id']);

        $peptide = Peptide::with('categories')->findOrFail($request->peptide_id);
        $generator = new SeoGeneratorService();

        if (!$generator->isConfigured()) {
            return response()->json(['success' => false, 'error' => 'Claude API key not configured.']);
        }

        $overview = $generator->rewritePeptideOverview($peptide);

        if (!$overview) {
            return response()->json(['success' => false, 'error' => 'AI generation failed.']);
        }

        return response()->json([
            'success' => true,
            'peptide_id' => $peptide->id,
            'overview' => $overview,
        ]);
    }

    public function generateBlogOutline(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:200',
            'keyword' => 'nullable|string|max:100',
        ]);

        $generator = new SeoGeneratorService();

        if (!$generator->isConfigured()) {
            return response()->json(['success' => false, 'error' => 'Claude API key not configured.']);
        }

        $outline = $generator->generateBlogOutline($request->topic, $request->keyword);

        if (!$outline) {
            return response()->json(['success' => false, 'error' => 'AI generation failed.']);
        }

        return response()->json(['success' => true, 'outline' => $outline]);
    }

    public function review()
    {
        $peptides = Peptide::published()
            ->with('categories')
            ->select('id', 'name', 'slug', 'meta_title', 'meta_description', 'updated_at')
            ->orderBy('name')
            ->get();

        $blogPosts = BlogPost::published()
            ->select('id', 'title', 'slug', 'meta_title', 'meta_description', 'updated_at')
            ->latest('published_at')
            ->get();

        $pages = Page::where('status', 'published')
            ->whereNull('variant_of')
            ->select('id', 'title', 'slug', 'meta_title', 'meta_description', 'updated_at')
            ->orderBy('title')
            ->get();

        // Static pages with SEO stored in Settings table
        $staticPages = [
            ['key' => 'home', 'name' => 'Homepage', 'path' => '/', 'title_key' => 'home_title', 'desc_key' => 'home_description'],
            ['key' => 'peptides_index', 'name' => 'Peptide Database', 'path' => '/peptides', 'title_key' => 'peptides_index_title', 'desc_key' => 'peptides_index_description'],
            ['key' => 'blog_index', 'name' => 'Blog Index', 'path' => '/blog', 'title_key' => 'blog_index_title', 'desc_key' => 'blog_index_description'],
            ['key' => 'calculator', 'name' => 'Calculator', 'path' => '/calculator', 'title_key' => 'calculator_title', 'desc_key' => 'calculator_description'],
        ];

        // Load current values from Settings
        foreach ($staticPages as &$sp) {
            $sp['meta_title'] = Setting::getValue('seo_pages', $sp['title_key'], '');
            $sp['meta_description'] = Setting::getValue('seo_pages', $sp['desc_key'], '');
        }
        unset($sp);

        return view('admin.settings.seo-review', compact('peptides', 'blogPosts', 'pages', 'staticPages'));
    }

    public function updateSeo(Request $request)
    {
        $request->validate([
            'type' => 'required|in:peptide,blog_post,page,static_page',
            'id' => 'required',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
        ]);

        if ($request->type === 'static_page') {
            // Static pages store SEO in Settings table
            $validKeys = ['home', 'peptides_index', 'blog_index', 'calculator'];
            $key = $request->id;
            if (!in_array($key, $validKeys)) {
                return response()->json(['success' => false, 'error' => 'Invalid page key.']);
            }
            Setting::setValue('seo_pages', $key . '_title', $request->meta_title ?? '');
            Setting::setValue('seo_pages', $key . '_description', $request->meta_description ?? '');
            return response()->json(['success' => true]);
        }

        $model = match ($request->type) {
            'peptide' => Peptide::findOrFail($request->id),
            'blog_post' => BlogPost::findOrFail($request->id),
            'page' => Page::findOrFail($request->id),
        };

        $model->update([
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        return response()->json(['success' => true]);
    }
}
