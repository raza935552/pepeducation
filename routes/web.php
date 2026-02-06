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
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Pages
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/disclaimer', [PageController::class, 'disclaimer'])->name('disclaimer');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');

// Public Peptide Routes
Route::get('/peptides', [PeptideController::class, 'index'])->name('peptides.index');
Route::get('/peptides/{peptide}', [PeptideController::class, 'show'])->name('peptides.show');

// Calculator Route
Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator');

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

// Outbound Link Tracking
Route::get('/go/{slug}', [OutboundController::class, 'track'])->name('outbound.track');

// Quizzes
Route::get('/quiz/{slug}', [QuizController::class, 'show'])->name('quiz.show');
Route::get('/quiz/{slug}/embed', [QuizController::class, 'embed'])->name('quiz.embed');

// Lead Magnets
Route::get('/lead-magnet/{slug}', [LeadMagnetController::class, 'landing'])->name('lead-magnet.landing');
Route::get('/lead-magnet/{slug}/download', [LeadMagnetController::class, 'download'])->name('lead-magnet.download');

// Public form submissions (from page builder forms)
Route::post('/form-submit', [\App\Http\Controllers\FormSubmitController::class, 'store'])
    ->name('form.submit')
    ->middleware('throttle:10,1');

// Auth routes (must come before catch-all)
require __DIR__.'/auth.php';

// Admin routes (must come before catch-all)
require __DIR__.'/admin.php';

// Dynamic Pages (must be last to not conflict with other routes)
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-z0-9][a-z0-9\-]*')
    ->name('page.show');
