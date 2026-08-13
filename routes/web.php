<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeptideController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\OutboundController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\LeadMagnetController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\MaintenanceController;
use App\Models\StackGoal;
use Illuminate\Support\Facades\Route;

// Maintenance mode bypass
Route::post('/maintenance/unlock', [MaintenanceController::class, 'unlock'])
    ->name('maintenance.unlock');

Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/disclaimer', [PageController::class, 'disclaimer'])->name('disclaimer');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// Public Peptide Routes
Route::get('/peptides', [PeptideController::class, 'index'])->name('peptides.index');
// Comparison routes must be registered BEFORE the {peptide} wildcard
Route::get('/peptides/compare', [\App\Http\Controllers\PeptideComparisonController::class, 'index'])->name('peptides.compare');
Route::get('/peptides/compare/{slugA}/vs/{slugB}', [\App\Http\Controllers\PeptideComparisonController::class, 'index'])
    ->where(['slugA' => '[a-z0-9][a-z0-9\-]*', 'slugB' => '[a-z0-9][a-z0-9\-]*'])
    ->name('peptides.compare.pair');
Route::get('/peptides/{peptide}', [PeptideController::class, 'show'])->name('peptides.show');

// Calculators — hub + individual tools
Route::get('/calculators', [CalculatorController::class, 'index'])->name('calculators.index');
Route::get('/calculators/{calculator}/embed', [CalculatorController::class, 'embed'])
    ->where('calculator', '[a-z0-9-]+')
    ->name('calculators.embed');
Route::get('/calculators/{calculator}', [CalculatorController::class, 'show'])
    ->where('calculator', '[a-z0-9-]+')
    ->name('calculators.show');
// Legacy single-calculator URL → 301 to the reconstitution tool
Route::get('/calculator', [CalculatorController::class, 'legacyRedirect'])->name('calculator');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Account Routes
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::get('/bookmarks', [AccountController::class, 'bookmarks'])->name('bookmarks');
        Route::get('/preferences', [AccountController::class, 'preferences'])->name('preferences');
        Route::put('/preferences', [AccountController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/contributions', [AccountController::class, 'contributions'])->name('contributions');
    });

    // Bookmark Toggle
    Route::post('/bookmarks/{peptide}', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
});

/*
|--------------------------------------------------------------------------
| Community (members-only forum) — private, never indexed, never scraped
|--------------------------------------------------------------------------
| auth + verified gate participation; `community` enforces the feature flag
| (dark launch) + noindex header; `community.scrape` blocks bots/AI scrapers.
*/
Route::middleware(['auth', 'verified', 'community', 'community.scrape'])
    ->prefix('community')
    ->name('community.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Community\CommunityController::class, 'index'])->name('index');
        Route::get('/search', [\App\Http\Controllers\Community\CommunityController::class, 'search'])->name('search');
        Route::get('/members/{user:slug}', [\App\Http\Controllers\Community\CommunityController::class, 'member'])->name('members.show');
        Route::get('/c/{category:slug}', [\App\Http\Controllers\Community\CommunityController::class, 'category'])->name('category');

        Route::get('/threads/create', [\App\Http\Controllers\Community\ThreadController::class, 'create'])->name('threads.create');
        Route::post('/threads', [\App\Http\Controllers\Community\ThreadController::class, 'store'])->name('threads.store');
        Route::get('/t/{thread:slug}', [\App\Http\Controllers\Community\ThreadController::class, 'show'])->name('threads.show');
        Route::get('/t/{thread:slug}/edit', [\App\Http\Controllers\Community\ThreadController::class, 'edit'])->name('threads.edit');
        Route::patch('/t/{thread:slug}', [\App\Http\Controllers\Community\ThreadController::class, 'update'])->name('threads.update');
        Route::delete('/t/{thread:slug}', [\App\Http\Controllers\Community\ThreadController::class, 'destroy'])->name('threads.destroy');

        Route::post('/t/{thread:slug}/replies', [\App\Http\Controllers\Community\ReplyController::class, 'store'])->name('replies.store');
        Route::patch('/replies/{post}', [\App\Http\Controllers\Community\ReplyController::class, 'update'])->name('replies.update');
        Route::delete('/replies/{post}', [\App\Http\Controllers\Community\ReplyController::class, 'destroy'])->name('replies.destroy');
        Route::post('/replies/{post}/solution', [\App\Http\Controllers\Community\ReplyController::class, 'toggleSolution'])->name('replies.solution');

        // Engagement (AJAX)
        Route::post('/react', [\App\Http\Controllers\Community\ReactionController::class, 'toggle'])->name('react');
        Route::post('/report', [\App\Http\Controllers\Community\ReportController::class, 'store'])->name('report');
        Route::post('/t/{thread:slug}/subscribe', [\App\Http\Controllers\Community\SubscriptionController::class, 'toggle'])->name('subscribe');
    });

// Outbound Link Tracking
Route::get('/go/{slug}', [OutboundController::class, 'track'])->name('outbound.track');

// Paid-ad bridge landers (Operator Brief) — season the pixel, CTA -> /go -> Biolinx
Route::get('/lp/{slug}', [\App\Http\Controllers\LanderController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('lander.show');

// Quizzes
Route::get('/quiz/{slug}', [QuizController::class, 'show'])->name('quiz.show');
Route::get('/quiz/{slug}/embed', [QuizController::class, 'embed'])->name('quiz.embed');
Route::post('/quiz/abandon', [QuizController::class, 'abandon'])->name('quiz.abandon');

// Subscriber sync (from email popup / Customer.io)
Route::post('/subscriber/sync', [\App\Http\Controllers\SubscriberSyncController::class, 'sync'])->name('subscriber.sync');

// Lead Magnets
Route::get('/lead-magnet/{slug}', [LeadMagnetController::class, 'landing'])->name('lead-magnet.landing');
Route::get('/lead-magnet/{slug}/download', [LeadMagnetController::class, 'download'])->name('lead-magnet.download');

// Public form submissions (from page builder forms)
Route::post('/form-submit', [\App\Http\Controllers\FormSubmitController::class, 'store'])
    ->name('form.submit');

// Live chat widget (public, visitor-facing). Polling-based.
Route::prefix('chat')->name('chat.')->group(function () {
    Route::post('/init', [\App\Http\Controllers\ChatWidgetController::class, 'init'])->name('init')->middleware('throttle:60,1');
    Route::post('/start', [\App\Http\Controllers\ChatWidgetController::class, 'start'])->name('start')->middleware('throttle:8,1');
    Route::post('/send', [\App\Http\Controllers\ChatWidgetController::class, 'send'])->name('send')->middleware('throttle:30,1');
    Route::post('/handoff', [\App\Http\Controllers\ChatWidgetController::class, 'handoff'])->name('handoff')->middleware('throttle:6,1');
    Route::post('/rate', [\App\Http\Controllers\ChatWidgetController::class, 'rate'])->name('rate')->middleware('throttle:10,1');
    Route::get('/poll', [\App\Http\Controllers\ChatWidgetController::class, 'poll'])->name('poll')->middleware('throttle:120,1');
});

// Auth routes (must come before catch-all)
require __DIR__.'/auth.php';

// Admin routes (must come before catch-all)
require __DIR__.'/admin.php';

// Pep Guide (Botpress chatbot)
Route::get('/pep-guide', fn() => view('pep-guide'))->name('pep-guide');

// Stack Builder
Route::get('/stack-builder', fn() => view('stack-builder.index'))->name('stack-builder');
Route::get('/stack-builder/{goal:slug}', fn(StackGoal $goal) => view('stack-builder.index', ['goalSlug' => $goal->slug]))->name('stack-builder.goal');

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Buy CTA click tracking endpoint (fire-and-forget, no auth needed)
Route::post('/track/buy-click', function (\Illuminate\Http\Request $request) {
    try {
        \Illuminate\Support\Facades\DB::table('buy_clicks')->insert([
            'peptide_id' => is_numeric($request->input('peptide_id')) ? (int) $request->input('peptide_id') : null,
            'context' => mb_substr((string) $request->input('context', ''), 0, 40),
            'destination' => mb_substr((string) $request->input('destination', 'BioLinx Labs'), 0, 60),
            'source_url' => mb_substr((string) $request->input('source_url', $request->headers->get('Referer', '')), 0, 500),
            'target_url' => mb_substr((string) $request->input('target_url', ''), 0, 500),
            'has_product' => (bool) $request->input('has_product', false),
            'ip_hash' => hash('sha256', $request->ip().config('app.key')),
            'user_agent_short' => mb_substr((string) $request->userAgent(), 0, 60),
            'created_at' => now(),
        ]);
    } catch (\Throwable $e) {
        // silently fail - tracking should never break user flow
    }

    return response()->json(['ok' => true]);
})->name('track.buy-click');

// IndexNow key verification file (Bing/Yandex). Filename is the key itself.
Route::get('/{key}.txt', function (string $key) {
    $stored = \App\Models\Setting::getValue('seo', 'indexnow_key');
    if ($stored && hash_equals($stored, $key)) {
        return response($stored, 200)->header('Content-Type', 'text/plain');
    }
    abort(404);
})->where('key', '[a-f0-9]{16,128}');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{category}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/tag/{tag}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->where('slug', '[a-z0-9][a-z0-9\-]*')->name('blog.show');

// Author bio pages
Route::get('/author/{user}', [\App\Http\Controllers\AuthorController::class, 'show'])->name('author.show');

// Best Peptides for {goal} — ranked roundup pages
Route::get('/best-peptides', [\App\Http\Controllers\PeptideGoalController::class, 'index'])->name('peptide-goals.index');
Route::get('/best-peptides-for-{goal}', [\App\Http\Controllers\PeptideGoalController::class, 'show'])
    ->where('goal', '[a-z0-9-]+')
    ->name('peptide-goals.show');

// Where to buy — hub + per-peptide buying guides (BioLinx-focused)
Route::get('/where-to-buy', [\App\Http\Controllers\WhereToBuyController::class, 'index'])->name('where-to-buy');
Route::get('/where-to-buy-{peptide}', [\App\Http\Controllers\WhereToBuyController::class, 'show'])
    ->where('peptide', '[a-z0-9-]+')
    ->name('where-to-buy.show');

// Dynamic Pages (must be last to not conflict with other routes)
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-z0-9][a-z0-9\-]*')
    ->name('page.show');

// Dev-request intake for the PP Telegram pipeline (@ppsystemai_bot), secret-verified.
Route::post('webhooks/telegram-intake', [\App\Http\Controllers\Webhooks\TelegramIntakeController::class, 'handle'])
    ->name('webhooks.telegram-intake');
