<!-- Template Selection Modal -->
<div id="template-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 {{ $page ? 'hidden' : '' }}">
    <div class="absolute inset-0 bg-black/70 modal-backdrop"></div>
    <div class="relative bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-700">
            <div>
                <h2 class="text-xl font-bold text-white">Choose a Template</h2>
                <p class="text-sm text-gray-400 mt-1">Start with a pre-built template or begin from scratch</p>
            </div>
            <button type="button" class="close-modal-btn text-gray-400 hover:text-white p-2">
                <svg aria-hidden="true" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Template Grid -->
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <!-- Category Tabs -->
            <div id="template-categories" class="flex gap-2 mb-6">
                <button data-category="all" class="template-cat-btn active px-4 py-2 rounded-lg text-sm font-medium">
                    All Templates
                </button>
                <button data-category="advertorial" class="template-cat-btn px-4 py-2 rounded-lg text-sm font-medium">
                    Advertorial
                </button>
                <button data-category="listicle" class="template-cat-btn px-4 py-2 rounded-lg text-sm font-medium">
                    Listicle
                </button>
                <button data-category="landing" class="template-cat-btn px-4 py-2 rounded-lg text-sm font-medium">
                    Landing Page
                </button>
                <button data-category="custom" class="template-cat-btn px-4 py-2 rounded-lg text-sm font-medium">
                    My Templates
                </button>
            </div>

            <!-- Templates Grid -->
            <div id="templates-grid" class="grid grid-cols-3 gap-4">
                <!-- Blank Template (Always first) -->
                <div class="template-card cursor-pointer group" data-template-id="blank">
                    <div class="aspect-[4/3] bg-gray-700 rounded-lg border-2 border-dashed border-gray-600 flex items-center justify-center group-hover:border-blue-500 transition-colors">
                        <div class="text-center">
                            <svg aria-hidden="true" class="w-12 h-12 mx-auto text-gray-500 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-400">Start Fresh</p>
                        </div>
                    </div>
                    <h3 class="mt-3 font-medium text-white">Blank Page</h3>
                    <p class="text-sm text-gray-400">Start from scratch</p>
                </div>

                <!-- Loading placeholder -->
                <div id="templates-loading" class="col-span-2 flex items-center justify-center py-12">
                    <svg aria-hidden="true" class="w-8 h-8 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save as Template Modal -->
<div id="save-template-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/70 save-modal-backdrop"></div>
    <div class="relative bg-gray-800 rounded-xl shadow-2xl w-full max-w-md">
        <div class="p-6">
            <h2 class="text-lg font-bold text-white mb-4">Save as Template</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Template Name</label>
                    <input type="text" id="template-name"
                           class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2"
                           placeholder="My Awesome Template">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                    <textarea id="template-description" rows="2"
                              class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2"
                              placeholder="Brief description of this template"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                    <select id="template-category"
                            class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2">
                        <option value="custom">Custom</option>
                        <option value="advertorial">Advertorial</option>
                        <option value="listicle">Listicle</option>
                        <option value="landing">Landing Page</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" class="cancel-save-btn flex-1 px-4 py-2 text-gray-300 border border-gray-600 rounded-lg hover:bg-gray-700">
                    Cancel
                </button>
                <button type="button" id="save-template-btn" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Template
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .template-cat-btn { color: #9ca3af; background: #374151; }
    .template-cat-btn:hover { color: #fff; }
    .template-cat-btn.active { color: #fff; background: #3b82f6; }
    .template-card.selected .aspect-\[4\/3\] { border-color: #3b82f6 !important; border-style: solid !important; }
</style>
