<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PeptideController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ContributionController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\PeptideRequestController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SupporterController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\QuizQuestionController;
use App\Http\Controllers\Admin\QuizOutcomeController;
use App\Http\Controllers\Admin\PopupController;
use App\Http\Controllers\Admin\LeadMagnetController;
use App\Http\Controllers\Admin\OutboundLinkController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostVersionController;
use App\Http\Controllers\Admin\StackGoalController;
use App\Http\Controllers\Admin\StackProductController;
use App\Http\Controllers\Admin\StackBundleController;
use App\Http\Controllers\Admin\StackStoreController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ResultsBankController;
use App\Http\Controllers\Admin\UnsplashController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PageVersionController;
use App\Http\Controllers\Admin\PageAnalyticsController;
use App\Http\Controllers\Admin\SavedSectionController;
use App\Http\Controllers\Admin\FormSubmissionController;
use App\Http\Controllers\Admin\AiContentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

    // Peptides
    Route::resource('peptides', PeptideController::class);
    Route::patch('peptides/{peptide}/toggle-publish', [PeptideController::class, 'togglePublish'])
        ->name('peptides.toggle-publish');

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Users
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('users/{user}/suspend', [UserController::class, 'toggleSuspend'])->name('users.suspend');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Contributions
    Route::get('contributions', [ContributionController::class, 'index'])->name('contributions.index');
    Route::get('contributions/{contribution}', [ContributionController::class, 'show'])->name('contributions.show');
    Route::post('contributions/{contribution}/approve', [ContributionController::class, 'approve'])->name('contributions.approve');
    Route::post('contributions/{contribution}/reject', [ContributionController::class, 'reject'])->name('contributions.reject');

    // Contact Messages
    Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
    Route::patch('messages/{message}/status', [ContactMessageController::class, 'updateStatus'])->name('messages.status');
    Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

    // Peptide Requests
    Route::get('requests', [PeptideRequestController::class, 'index'])->name('requests.index');
    Route::get('requests/{peptideRequest}', [PeptideRequestController::class, 'show'])->name('requests.show');
    Route::patch('requests/{peptideRequest}/status', [PeptideRequestController::class, 'updateStatus'])->name('requests.status');
    Route::delete('requests/{peptideRequest}', [PeptideRequestController::class, 'destroy'])->name('requests.destroy');

    // Subscribers
    Route::get('subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
    Route::get('subscribers/export', [SubscriberController::class, 'export'])->name('subscribers.export');
    Route::get('subscribers/{subscriber}', [SubscriberController::class, 'show'])->name('subscribers.show');
    Route::get('subscribers/{subscriber}/profile', [SubscriberController::class, 'profile'])->name('subscribers.profile');
    Route::delete('subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');

    // Pages (Page Builder)
    Route::resource('pages', PageController::class);
    Route::post('pages/{page}/duplicate', [PageController::class, 'duplicate'])->name('pages.duplicate');
    Route::post('pages/{page}/variant', [PageController::class, 'createVariant'])->name('pages.variant');
    Route::post('pages/upload-image', [PageController::class, 'uploadImage'])->name('pages.upload-image')->middleware('throttle:30,1');

    // Page Versions
    Route::get('pages/{page}/versions', [PageVersionController::class, 'index'])->name('pages.versions');
    Route::post('pages/{page}/versions/{version}/restore', [PageVersionController::class, 'restore'])->name('pages.versions.restore');

    // Page Analytics
    Route::get('pages/{page}/analytics', [PageAnalyticsController::class, 'show'])->name('pages.analytics');

    // Saved Sections (Reusable)
    Route::get('sections', [SavedSectionController::class, 'index'])->name('sections.index');
    Route::post('sections', [SavedSectionController::class, 'store'])->name('sections.store');
    Route::get('sections/{section}', [SavedSectionController::class, 'show'])->name('sections.show');
    Route::delete('sections/{section}', [SavedSectionController::class, 'destroy'])->name('sections.destroy');

    // Form Submissions
    Route::get('form-submissions', [FormSubmissionController::class, 'index'])->name('form-submissions.index');
    Route::delete('form-submissions/{submission}', [FormSubmissionController::class, 'destroy'])->name('form-submissions.destroy');

    // Page Templates
    Route::get('templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::post('templates', [TemplateController::class, 'store'])->name('templates.store');
    Route::get('templates/{template}', [TemplateController::class, 'show'])->name('templates.show');
    Route::delete('templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');

    // Supporters
    Route::resource('supporters', SupporterController::class)->except(['show']);

    // Quiz Results Bank (Peptide Recommendations)
    Route::resource('results-bank', ResultsBankController::class)->except(['show']);

    // Marketing: Quizzes
    Route::resource('quizzes', QuizController::class);
    Route::post('quizzes/{quiz}/duplicate', [QuizController::class, 'duplicate'])->name('quizzes.duplicate');
    Route::get('quizzes/{quiz}/analytics', [QuizController::class, 'analytics'])->name('quizzes.analytics');
    Route::post('quizzes/{quiz}/questions', [QuizQuestionController::class, 'store'])->name('quizzes.questions.store');
    Route::put('quizzes/{quiz}/questions/{question}', [QuizQuestionController::class, 'update'])->name('quizzes.questions.update');
    Route::delete('quizzes/{quiz}/questions/{question}', [QuizQuestionController::class, 'destroy'])->name('quizzes.questions.destroy');
    Route::post('quizzes/{quiz}/questions/reorder', [QuizQuestionController::class, 'reorder'])->name('quizzes.questions.reorder');
    Route::post('quizzes/{quiz}/outcomes', [QuizOutcomeController::class, 'store'])->name('quizzes.outcomes.store');
    Route::put('quizzes/{quiz}/outcomes/{outcome}', [QuizOutcomeController::class, 'update'])->name('quizzes.outcomes.update');
    Route::delete('quizzes/{quiz}/outcomes/{outcome}', [QuizOutcomeController::class, 'destroy'])->name('quizzes.outcomes.destroy');

    // Marketing: Popups
    Route::resource('popups', PopupController::class);
    Route::post('popups/{popup}/duplicate', [PopupController::class, 'duplicate'])->name('popups.duplicate');

    // Marketing: Lead Magnets
    Route::resource('lead-magnets', LeadMagnetController::class);
    Route::post('lead-magnets/{leadMagnet}/duplicate', [LeadMagnetController::class, 'duplicate'])->name('lead-magnets.duplicate');

    // Marketing: Outbound Links
    Route::resource('outbound-links', OutboundLinkController::class);

    // Stack Builder
    Route::resource('stack-goals', StackGoalController::class)->except(['show']);
    Route::resource('stack-products', StackProductController::class)->except(['show']);
    Route::resource('stack-stores', StackStoreController::class)->except(['show']);
    Route::post('stack-products/upload-image', [StackProductController::class, 'uploadImage'])->name('stack-products.upload-image');
    Route::resource('stack-bundles', StackBundleController::class)->except(['show']);
    Route::post('stack-bundles/{stackBundle}/items', [StackBundleController::class, 'storeItem'])->name('stack-bundles.items.store');
    Route::put('stack-bundles/{stackBundle}/items/{item}', [StackBundleController::class, 'updateItem'])->name('stack-bundles.items.update');
    Route::delete('stack-bundles/{stackBundle}/items/{item}', [StackBundleController::class, 'destroyItem'])->name('stack-bundles.items.destroy');

    // Blog
    Route::resource('blog-posts', BlogPostController::class);
    Route::post('blog-posts/{blogPost}/duplicate', [BlogPostController::class, 'duplicate'])->name('blog-posts.duplicate');
    Route::patch('blog-posts/{blogPost}/toggle-featured', [BlogPostController::class, 'toggleFeatured'])->name('blog-posts.toggle-featured');
    Route::post('blog-posts/upload-image', [BlogPostController::class, 'uploadImage'])->name('blog-posts.upload-image')->middleware('throttle:30,1');
    Route::get('blog-posts/{blogPost}/versions', [BlogPostVersionController::class, 'index'])->name('blog-posts.versions');
    Route::post('blog-posts/{blogPost}/versions/{version}/restore', [BlogPostVersionController::class, 'restore'])->name('blog-posts.versions.restore');

    // Blog Categories
    Route::get('blog-categories', [BlogCategoryController::class, 'index'])->name('blog-categories.index');
    Route::post('blog-categories', [BlogCategoryController::class, 'store'])->name('blog-categories.store');
    Route::put('blog-categories/{blogCategory}', [BlogCategoryController::class, 'update'])->name('blog-categories.update');
    Route::delete('blog-categories/{blogCategory}', [BlogCategoryController::class, 'destroy'])->name('blog-categories.destroy');

    // Media Library (Page Builder)
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::delete('media', [MediaController::class, 'destroy'])->name('media.destroy');

    // Unsplash (Stock Photos for Page Builder)
    Route::get('unsplash/search', [UnsplashController::class, 'search'])->name('unsplash.search')->middleware('throttle:60,1');
    Route::post('unsplash/track-download', [UnsplashController::class, 'trackDownload'])->name('unsplash.track-download');

    // AI Content Generation (Page Builder)
    Route::post('ai-content/generate', [AiContentController::class, 'generate'])->name('ai-content.generate')->middleware('throttle:20,1');

    // Settings (Integrations)
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
});
