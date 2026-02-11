<!-- Autosave Recovery Banner (hidden by default) -->
<div id="recovery-banner" class="hidden fixed top-0 left-0 right-0 z-50 bg-amber-600 text-white px-4 py-3 flex items-center justify-between shadow-lg">
    <div class="flex items-center gap-3">
        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span id="recovery-message">Unsaved changes found from a previous session</span>
    </div>
    <div class="flex items-center gap-2">
        <button type="button" onclick="restoreRecovery()" class="px-4 py-1.5 bg-white text-amber-700 rounded font-medium text-sm hover:bg-amber-50 transition-colors">
            Restore
        </button>
        <button type="button" onclick="dismissRecovery()" class="px-4 py-1.5 bg-amber-700 text-white rounded font-medium text-sm hover:bg-amber-800 transition-colors">
            Discard
        </button>
    </div>
</div>

<!-- Top Toolbar -->
<div class="h-14 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-4">
    <!-- Left: Back & Title -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.blog-posts.index') }}" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="text-sm">Back to Posts</span>
        </a>
        <div class="h-6 w-px bg-gray-700"></div>
        <input type="text" id="page-title" value="{{ $blogPost->title ?? '' }}"
               placeholder="Post Title"
               class="bg-transparent border-0 text-white text-lg font-medium focus:ring-0 focus:outline-none w-64 placeholder-gray-500">
    </div>

    <!-- Center: Undo/Redo + Device Switcher -->
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-1">
            <button type="button" onclick="editorUndo()" id="btn-undo"
                    class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Undo (Ctrl+Z)">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
            </button>
            <button type="button" onclick="editorRedo()" id="btn-redo"
                    class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Redo (Ctrl+Y)">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/>
                </svg>
            </button>
        </div>

        <div class="h-6 w-px bg-gray-700"></div>

        <div class="flex items-center gap-1 bg-gray-900 rounded-lg p-1">
            <button type="button" onclick="setDevice('Desktop')" id="btn-desktop"
                    class="device-btn active px-3 py-1.5 rounded text-sm font-medium transition-colors">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </button>
            <button type="button" onclick="setDevice('Tablet')" id="btn-tablet"
                    class="device-btn px-3 py-1.5 rounded text-sm font-medium transition-colors">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </button>
            <button type="button" onclick="setDevice('Mobile')" id="btn-mobile"
                    class="device-btn px-3 py-1.5 rounded text-sm font-medium transition-colors">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Right: Actions -->
    <div class="flex items-center gap-3">
        <div id="autosave-status" class="flex items-center gap-2 text-sm text-gray-400">
            <span id="autosave-indicator" class="hidden">
                <span class="autosave-saving hidden flex items-center gap-1">
                    <svg aria-hidden="true" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </span>
                <span class="autosave-dirty hidden flex items-center gap-1">
                    <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                    Unsaved
                </span>
                <span class="autosave-saved hidden flex items-center gap-1 text-green-500">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="autosave-time">Saved</span>
                </span>
            </span>
        </div>

        <button type="button" onclick="openMediaLibrary()" class="text-gray-400 hover:text-white p-2 rounded transition-colors" title="Media Library">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </button>
        <button type="button" onclick="openUnsplashModal()" class="text-gray-400 hover:text-white p-2 rounded transition-colors" title="Stock Photos (Unsplash)">
            <svg aria-hidden="true" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7.5 6.75V0h9v6.75h-9zm9 3.75H24V24H0V10.5h7.5v6.75h9V10.5z"/></svg>
        </button>
        <button type="button" onclick="openVersionHistory()" class="text-gray-400 hover:text-white p-2 rounded transition-colors" title="Version History">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </button>
        <button type="button" onclick="openAiContent()" class="text-gray-400 hover:text-white p-2 rounded transition-colors" title="AI Content">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </button>
        <button type="button" onclick="toggleSettingsPanel()" class="text-gray-400 hover:text-white p-2 rounded transition-colors" title="Post Settings">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>
        @if($blogPost && $blogPost->isPublished())
        <a href="{{ route('blog.show', $blogPost->slug) }}" target="_blank" rel="noopener"
           class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white border border-gray-600 rounded-lg hover:border-gray-500 transition-colors">
            Preview
        </a>
        @endif
        <button type="button" onclick="savePost()" id="save-btn"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors flex items-center gap-2">
            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Save
        </button>
    </div>
</div>

<!-- Main Editor Area -->
<div class="flex-1 flex overflow-hidden">
    <!-- Left Panel: Blocks -->
    <div class="w-64 bg-gray-800 border-r border-gray-700 flex flex-col">
        <div class="flex border-b border-gray-700">
            <button type="button" onclick="showLeftPanel('blocks')" id="tab-blocks"
                    class="panel-tab active flex-1 px-4 py-3 text-sm font-medium transition-colors">
                Blocks
            </button>
            <button type="button" onclick="showLeftPanel('layers')" id="tab-layers"
                    class="panel-tab flex-1 px-4 py-3 text-sm font-medium transition-colors">
                Layers
            </button>
        </div>
        <div id="block-search-wrapper" class="p-3 border-b border-gray-700">
            <div class="relative">
                <input type="text" id="block-search" placeholder="Search blocks..."
                       class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg pl-9 pr-8 py-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
                <svg aria-hidden="true" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <button type="button" id="block-search-clear" class="hidden absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="blocks-panel" class="flex-1 overflow-y-auto p-3"></div>
        <div id="layers-panel" class="flex-1 overflow-y-auto p-3 hidden"></div>
    </div>

    <!-- Center: Canvas -->
    <div class="flex-1 bg-gray-900 overflow-hidden">
        <div id="gjs" class="h-full"></div>
    </div>

    <!-- Right Panel: Styles & Settings -->
    <div class="w-72 bg-gray-800 border-l border-gray-700 flex flex-col">
        <div class="flex border-b border-gray-700">
            <button type="button" onclick="showRightPanel('styles')" id="tab-styles"
                    class="panel-tab active flex-1 px-4 py-3 text-sm font-medium transition-colors">
                Styles
            </button>
            <button type="button" onclick="showRightPanel('traits')" id="tab-traits"
                    class="panel-tab flex-1 px-4 py-3 text-sm font-medium transition-colors">
                Settings
            </button>
        </div>
        <div id="selectors-panel" class="p-3 border-b border-gray-700"></div>
        <div id="styles-panel" class="flex-1 overflow-y-auto p-3"></div>
        <div id="traits-panel" class="flex-1 overflow-y-auto p-3 hidden"></div>
    </div>

    <!-- Post Settings Sidebar -->
    <div id="settings-panel" class="w-80 bg-gray-800 border-l border-gray-700 hidden flex-col overflow-hidden">
        <div class="p-4 border-b border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-white">Post Settings</h3>
            <button type="button" onclick="toggleSettingsPanel()" class="text-gray-400 hover:text-white">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                <select id="post-status" class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2">
                    <option value="draft" {{ ($blogPost->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ ($blogPost->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="scheduled" {{ ($blogPost->status ?? '') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                </select>
            </div>

            <!-- Published At -->
            <div id="published-at-wrapper" class="{{ ($blogPost->status ?? 'draft') === 'scheduled' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-300 mb-2">Publish Date</label>
                <input type="datetime-local" id="post-published-at"
                       value="{{ $blogPost && $blogPost->published_at ? $blogPost->published_at->format('Y-m-d\TH:i') : '' }}"
                       class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2">
            </div>

            <!-- Slug -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Slug</label>
                <div class="flex items-center">
                    <span class="text-gray-500 mr-1 text-sm">/blog/</span>
                    <input type="text" id="post-slug" value="{{ $blogPost->slug ?? '' }}"
                           class="flex-1 bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2"
                           placeholder="auto-generated">
                </div>
            </div>

            <!-- Excerpt -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Excerpt</label>
                <textarea id="post-excerpt" rows="3"
                          class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2"
                          placeholder="Short summary for cards and SEO">{{ $blogPost->excerpt ?? '' }}</textarea>
            </div>

            <!-- Featured -->
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="post-is-featured" {{ ($blogPost->is_featured ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-300">Featured Post</span>
                </label>
            </div>

            <!-- Categories -->
            <div class="pt-4 border-t border-gray-700">
                <h4 class="font-medium text-white mb-3">Categories</h4>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @php $selectedCategories = $blogPost ? $blogPost->categories->pluck('id')->toArray() : []; @endphp
                    @foreach($categories as $category)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" value="{{ $category->id }}"
                                   class="post-category w-4 h-4 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500"
                                   {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-300">{{ $category->name }}</span>
                        </label>
                    @endforeach
                    @if($categories->isEmpty())
                        <p class="text-sm text-gray-500">No categories yet. <a href="{{ route('admin.blog-categories.index') }}" class="text-blue-400 hover:underline">Create one</a></p>
                    @endif
                </div>
            </div>

            <!-- Tags -->
            <div class="pt-4 border-t border-gray-700">
                <h4 class="font-medium text-white mb-3">Tags</h4>
                <div class="space-y-2 max-h-32 overflow-y-auto mb-3">
                    @php $selectedTags = $blogPost ? $blogPost->tags->pluck('id')->toArray() : []; @endphp
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" value="{{ $tag->id }}"
                                   class="post-tag w-4 h-4 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500"
                                   {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-300">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
                <div>
                    <input type="text" id="post-new-tags" placeholder="Add new tags (comma-separated)"
                           class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-500 mt-1">e.g. peptide therapy, muscle growth</p>
                </div>
            </div>

            <!-- Related Peptides -->
            <div class="pt-4 border-t border-gray-700">
                <h4 class="font-medium text-white mb-3">Related Peptides</h4>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @php $selectedPeptides = $blogPost ? $blogPost->peptides->pluck('id')->toArray() : []; @endphp
                    @foreach($peptides as $peptide)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" value="{{ $peptide->id }}"
                                   class="post-peptide w-4 h-4 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500"
                                   {{ in_array($peptide->id, $selectedPeptides) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-300">{{ $peptide->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- SEO -->
            <div class="pt-4 border-t border-gray-700">
                <h4 class="font-medium text-white mb-4">SEO Settings</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Meta Title</label>
                        <input type="text" id="post-meta-title" value="{{ $blogPost->meta_title ?? '' }}"
                               class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2"
                               placeholder="SEO title (defaults to post title)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Meta Description</label>
                        <textarea id="post-meta-description" rows="3"
                                  class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2"
                                  placeholder="Brief description for search engines">{{ $blogPost->meta_description ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="pt-4 border-t border-gray-700">
                <h4 class="font-medium text-white mb-4">Featured Image</h4>
                <p class="text-xs text-gray-500 mb-3">Shown in post cards, social sharing, and article hero. Recommended: 1200x630px</p>
                <div id="og-image-preview" class="{{ ($blogPost->featured_image ?? null) ? '' : 'hidden' }} mb-3">
                    <div class="relative rounded-lg overflow-hidden bg-gray-700 aspect-[1200/630]">
                        <img id="og-image-img" src="{{ ($blogPost->featured_image ?? null) ? url($blogPost->featured_image) : '' }}"
                             alt="Featured Image" class="w-full h-full object-cover">
                        <button type="button" onclick="removeOgImage()" class="absolute top-2 right-2 p-1.5 bg-red-600 hover:bg-red-700 rounded text-white" title="Remove">
                            <svg aria-hidden="true" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <input type="hidden" id="post-featured-image" value="{{ $blogPost->featured_image ?? '' }}">
                <label class="cursor-pointer px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload Image
                    <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" id="og-image-upload">
                </label>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .device-btn { color: #9ca3af; }
    .device-btn:hover { color: #fff; background: rgba(255,255,255,0.1); }
    .device-btn.active { color: #fff; background: #3b82f6; }
    .panel-tab { color: #9ca3af; border-bottom: 2px solid transparent; }
    .panel-tab:hover { color: #fff; }
    .panel-tab.active { color: #fff; border-bottom-color: #3b82f6; }

    .gjs-one-bg { background-color: #1f2937 !important; }
    .gjs-two-color { color: #9ca3af !important; }
    .gjs-three-bg { background-color: #374151 !important; }
    .gjs-four-color, .gjs-four-color-h:hover { color: #3b82f6 !important; }

    .gjs-block {
        padding: 12px;
        margin: 4px;
        border-radius: 8px;
        background: #374151;
        border: 1px solid #4b5563;
        transition: all 0.15s;
    }
    .gjs-block:hover {
        border-color: #3b82f6;
        box-shadow: 0 0 0 1px #3b82f6;
    }
    .gjs-block__media { color: #9ca3af; }
    .gjs-block-label { color: #e5e7eb; font-size: 11px; margin-top: 6px; }

    .gjs-category-title {
        background: transparent !important;
        border-bottom: 1px solid #374151 !important;
        color: #9ca3af !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 11px !important;
        letter-spacing: 0.05em !important;
        padding: 12px 8px !important;
    }
    .gjs-category-open { border-color: #3b82f6 !important; }

    .gjs-sm-sector-title {
        background: #1f2937 !important;
        color: #e5e7eb !important;
        border-radius: 4px;
        margin-bottom: 4px;
    }
    .gjs-field { background: #374151 !important; border-color: #4b5563 !important; color: #fff !important; }
    .gjs-field:focus { border-color: #3b82f6 !important; }

    .gjs-layer { background: transparent; border-bottom: 1px solid #374151; }
    .gjs-layer:hover { background: rgba(59, 130, 246, 0.1); }
    .gjs-layer.gjs-selected { background: rgba(59, 130, 246, 0.2); }
    .gjs-layer-name { color: #e5e7eb; }

    .gjs-clm-tags { background: #1f2937 !important; }
    .gjs-clm-tag { background: #374151 !important; color: #e5e7eb !important; }

    #gjs { background: #111827; }
    .gjs-frame-wrapper { background: #f3f4f6; }
    .gjs-cv-canvas { background: #111827; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const postId = {{ $blogPost->id ?? 'null' }};
    let postSlug = {!! json_encode($blogPost->slug ?? null) !!};
    const isEdit = postId !== null;
    const projectData = @json($blogPost->content ?? null);
    let editor = null;
    let autosave = null;

    const autosaveId = postId ? 'blog_' + postId : (sessionStorage.getItem('gjs_new_blog_id') || (() => {
        const id = 'blog_new_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8);
        sessionStorage.setItem('gjs_new_blog_id', id);
        return id;
    })());

    editor = window.initGrapesJS({
        projectData: projectData,
        assets: [],
    });

    autosave = new window.GrapesJSAutosave(editor, {
        pageId: autosaveId,
        interval: 30000,
        onStatusChange: updateAutosaveUI,
    });

    checkForRecovery();

    function updateAutosaveUI(status) {
        const indicator = document.getElementById('autosave-indicator');
        const saving = indicator.querySelector('.autosave-saving');
        const dirty = indicator.querySelector('.autosave-dirty');
        const saved = indicator.querySelector('.autosave-saved');
        const timeSpan = indicator.querySelector('.autosave-time');

        indicator.classList.remove('hidden');
        saving.classList.add('hidden');
        dirty.classList.add('hidden');
        saved.classList.add('hidden');

        if (status.saving) {
            saving.classList.remove('hidden');
        } else if (status.dirty) {
            dirty.classList.remove('hidden');
        } else if (status.lastSaved) {
            saved.classList.remove('hidden');
            timeSpan.textContent = 'Saved ' + window.formatTimeAgo(status.lastSaved);
        }
    }

    function checkForRecovery() {
        if (autosave.hasRecoveryData()) {
            const meta = autosave.getRecoveryMeta();
            if (meta && (meta.pageId === autosaveId)) {
                const banner = document.getElementById('recovery-banner');
                const message = document.getElementById('recovery-message');
                const timeAgo = window.formatTimeAgo(new Date(meta.timestamp));
                message.textContent = 'Unsaved changes found from ' + timeAgo + (meta.title ? ' (' + meta.title + ')' : '');
                banner.classList.remove('hidden');
            }
        }
    }

    window.restoreRecovery = function() {
        if (autosave.restore()) {
            document.getElementById('recovery-banner').classList.add('hidden');
            showToast('Changes restored successfully', 'success');
        }
    };

    window.dismissRecovery = function() {
        autosave.clearRecoveryData();
        document.getElementById('recovery-banner').classList.add('hidden');
    };

    function showToast(message, type) {
        type = type || 'info';
        var toast = document.createElement('div');
        var bgClass = type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600';
        toast.className = 'fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white z-50 transition-opacity duration-300 ' + bgClass;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.classList.add('opacity-0');
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    }

    window.gjsAutosave = autosave;

    // Panel switching
    window.showLeftPanel = function(panel) {
        document.getElementById('blocks-panel').classList.toggle('hidden', panel !== 'blocks');
        document.getElementById('layers-panel').classList.toggle('hidden', panel !== 'layers');
        document.getElementById('block-search-wrapper').classList.toggle('hidden', panel !== 'blocks');
        document.getElementById('tab-blocks').classList.toggle('active', panel === 'blocks');
        document.getElementById('tab-layers').classList.toggle('active', panel === 'layers');
        if (panel !== 'blocks') clearBlockSearch();
    };

    var blockSearchInput = document.getElementById('block-search');
    var blockSearchClear = document.getElementById('block-search-clear');

    blockSearchInput.addEventListener('input', function(e) {
        var query = e.target.value.toLowerCase().trim();
        filterBlocks(query);
        blockSearchClear.classList.toggle('hidden', query.length === 0);
    });

    blockSearchClear.addEventListener('click', function() { clearBlockSearch(); });

    function clearBlockSearch() {
        blockSearchInput.value = '';
        blockSearchClear.classList.add('hidden');
        filterBlocks('');
    }

    function filterBlocks(query) {
        var blocksPanel = document.getElementById('blocks-panel');
        var blocks = blocksPanel.querySelectorAll('.gjs-block');
        var categories = blocksPanel.querySelectorAll('.gjs-block-category');

        if (!query) {
            blocks.forEach(function(block) { block.style.display = ''; });
            categories.forEach(function(cat) {
                cat.style.display = '';
                var title = cat.querySelector('.gjs-category-title');
                if (title && !title.classList.contains('gjs-category-open')) {
                    var content = cat.querySelector('.gjs-blocks-c');
                    if (content) content.style.display = 'none';
                }
            });
            return;
        }

        blocks.forEach(function(block) {
            var label = block.querySelector('.gjs-block-label');
            var labelText = label ? label.textContent.toLowerCase() : '';
            block.style.display = labelText.includes(query) ? '' : 'none';
        });

        categories.forEach(function(cat) {
            var visibleBlocks = cat.querySelectorAll('.gjs-block:not([style*="display: none"])');
            cat.style.display = visibleBlocks.length > 0 ? '' : 'none';
            if (visibleBlocks.length > 0) {
                var content = cat.querySelector('.gjs-blocks-c');
                if (content) content.style.display = '';
            }
        });
    }

    window.showRightPanel = function(panel) {
        document.getElementById('styles-panel').classList.toggle('hidden', panel !== 'styles');
        document.getElementById('traits-panel').classList.toggle('hidden', panel !== 'traits');
        document.getElementById('tab-styles').classList.toggle('active', panel === 'styles');
        document.getElementById('tab-traits').classList.toggle('active', panel === 'traits');
    };

    window.toggleSettingsPanel = function() {
        var panel = document.getElementById('settings-panel');
        panel.classList.toggle('hidden');
        panel.classList.toggle('flex');
    };

    window.setDevice = function(device) {
        editor.setDevice(device);
        document.querySelectorAll('.device-btn').forEach(function(btn) { btn.classList.remove('active'); });
        document.getElementById('btn-' + device.toLowerCase()).classList.add('active');
    };

    window.editorUndo = function() { if (editor) { editor.UndoManager.undo(); updateUndoRedoState(); } };
    window.editorRedo = function() { if (editor) { editor.UndoManager.redo(); updateUndoRedoState(); } };

    function updateUndoRedoState() {
        var um = editor.UndoManager;
        document.getElementById('btn-undo').disabled = !um.hasUndo();
        document.getElementById('btn-redo').disabled = !um.hasRedo();
    }

    editor.on('change:changesCount', updateUndoRedoState);

    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && !e.shiftKey && e.key === 'z') { e.preventDefault(); editorUndo(); }
        if ((e.ctrlKey || e.metaKey) && (e.shiftKey && e.key === 'z' || e.key === 'y')) { e.preventDefault(); editorRedo(); }
        if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); savePost(); }
    });

    updateUndoRedoState();

    // Show/hide published_at field based on status
    document.getElementById('post-status').addEventListener('change', function() {
        document.getElementById('published-at-wrapper').classList.toggle('hidden', this.value !== 'scheduled');
    });

    // Save post
    window.savePost = function() {
        var btn = document.getElementById('save-btn');
        var savingIcon = '<svg aria-hidden="true" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...';
        var savedIcon = '<svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        btn.textContent = '';
        btn.insertAdjacentHTML('beforeend', savingIcon);
        btn.disabled = true;

        var data = window.getGrapesJSData();

        var categories = Array.from(document.querySelectorAll('.post-category:checked')).map(function(el) { return el.value; });
        var tags = Array.from(document.querySelectorAll('.post-tag:checked')).map(function(el) { return el.value; });
        var peptides = Array.from(document.querySelectorAll('.post-peptide:checked')).map(function(el) { return el.value; });

        var formData = {
            title: document.getElementById('page-title').value,
            slug: document.getElementById('post-slug').value,
            status: document.getElementById('post-status').value,
            excerpt: document.getElementById('post-excerpt').value,
            meta_title: document.getElementById('post-meta-title').value,
            meta_description: document.getElementById('post-meta-description').value,
            featured_image: document.getElementById('post-featured-image').value,
            is_featured: document.getElementById('post-is-featured').checked ? '1' : '0',
            published_at: document.getElementById('post-published-at').value || null,
            categories: categories,
            tags: tags,
            new_tags: document.getElementById('post-new-tags').value,
            peptides: peptides,
            content: JSON.stringify(data.projectData),
            html: data.html + '<style>' + data.css + '</style>',
        };

        var url = isEdit ? '/admin/blog-posts/' + postSlug : '/admin/blog-posts';
        var method = isEdit ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(formData),
        })
        .then(function(response) {
            if (!response.ok) return response.json().then(function(err) { return Promise.reject(err); });
            return response.json();
        })
        .then(function(result) {
            btn.textContent = '';
            btn.insertAdjacentHTML('beforeend', savedIcon + ' Saved!');
            setTimeout(function() {
                btn.textContent = '';
                btn.insertAdjacentHTML('beforeend', savedIcon + ' Save');
                btn.disabled = false;
            }, 2000);

            if (autosave) autosave.onServerSave();

            document.getElementById('post-new-tags').value = '';

            if (!isEdit && result.post && result.post.slug) {
                postSlug = result.post.slug;
                window.history.replaceState({}, '', '/admin/blog-posts/' + result.post.slug + '/edit');
                sessionStorage.removeItem('gjs_new_blog_id');
                if (autosave) {
                    autosave.pageId = 'blog_' + result.post.id;
                    autosave.storageKey = 'gjs_autosave_blog_' + result.post.id;
                    autosave.metaKey = 'gjs_autosave_meta_blog_' + result.post.id;
                }
            }
        })
        .catch(function(error) {
            console.error('Save error:', error);
            var errorMsg = 'Error! Try again';
            if (error && error.errors) {
                var firstField = Object.keys(error.errors)[0];
                errorMsg = error.errors[firstField][0] || errorMsg;
            } else if (error && error.message) {
                errorMsg = error.message;
            }
            showToast(errorMsg, 'error');
            btn.textContent = 'Error! Try again';
            btn.classList.add('bg-red-600');
            setTimeout(function() {
                btn.textContent = '';
                btn.insertAdjacentHTML('beforeend', savedIcon + ' Save');
                btn.classList.remove('bg-red-600');
                btn.disabled = false;
            }, 3000);
        });
    };

    // OG Image upload
    document.getElementById('og-image-upload').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;
        var fd = new FormData();
        fd.append('image', file);
        fetch('/admin/blog-posts/upload-image', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: fd,
        })
        .then(function(r) {
            if (r.ok) {
                return r.json().then(function(result) {
                    var url = result.data[0];
                    document.getElementById('post-featured-image').value = url;
                    document.getElementById('og-image-img').src = url;
                    document.getElementById('og-image-preview').classList.remove('hidden');
                    showToast('Featured image uploaded', 'success');
                });
            } else {
                showToast('Upload failed (max 5MB, JPG/PNG/WebP)', 'error');
            }
        })
        .catch(function() { showToast('Upload failed', 'error'); });
        e.target.value = '';
    });

    window.removeOgImage = function() {
        document.getElementById('post-featured-image').value = '';
        document.getElementById('og-image-preview').classList.add('hidden');
        document.getElementById('og-image-img').src = '';
    };
});
</script>
@endpush
