<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
    {{-- Engagement Score --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Score</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subscriber->engagement_score ?? 0 }}</p>
    </div>

    {{-- Sessions --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Sessions</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subscriber->total_sessions ?? 0 }}</p>
    </div>

    {{-- Page Views --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Page Views</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subscriber->total_page_views ?? 0 }}</p>
    </div>

    {{-- Shop Clicks --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Shop Clicks</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subscriber->shop_clicks ?? 0 }}</p>
    </div>

    {{-- Quiz Responses --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Quizzes</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subscriber->quizResponses->count() }}</p>
    </div>

    {{-- Downloads --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Downloads</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subscriber->leadMagnetDownloads->count() }}</p>
    </div>
</div>
