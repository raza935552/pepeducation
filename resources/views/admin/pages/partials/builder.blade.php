@include('admin.pages.partials.template-modal')

<!-- Autosave Recovery Banner (hidden by default) -->
<div id="recovery-banner" class="hidden fixed top-0 left-0 right-0 z-50 bg-amber-600 text-white px-4 py-3 flex items-center justify-between shadow-lg">
    <div class="flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="text-sm">Back to Pages</span>
        </a>
        <div class="h-6 w-px bg-gray-700"></div>
        <input type="text" id="page-title" value="{{ $page->title ?? '' }}"
               placeholder="Page Title"
               class="bg-transparent border-0 text-white text-lg font-medium focus:ring-0 focus:outline-none w-64 placeholder-gray-500">
    </div>

    <!-- Center: Undo/Redo + Device Switcher -->
    <div class="flex items-center gap-3">
        <!-- Undo/Redo -->
        <div class="flex items-center gap-1">
            <button type="button" onclick="editorUndo()" id="btn-undo"
                    class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Undo (Ctrl+Z)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
            </button>
            <button type="button" onclick="editorRedo()" id="btn-redo"
                    class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Redo (Ctrl+Y)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/>
                </svg>
            </button>
        </div>

        <div class="h-6 w-px bg-gray-700"></div>

        <!-- Device Switcher -->
        <div class="flex items-center gap-1 bg-gray-900 rounded-lg p-1">
            <button type="button" onclick="setDevice('Desktop')" id="btn-desktop"
                    class="device-btn active px-3 py-1.5 rounded text-sm font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </button>
            <button type="button" onclick="setDevice('Tablet')" id="btn-tablet"
                    class="device-btn px-3 py-1.5 rounded text-sm font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </button>
            <button type="button" onclick="setDevice('Mobile')" id="btn-mobile"
                    class="device-btn px-3 py-1.5 rounded text-sm font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Right: Actions -->
    <div class="flex items-center gap-3">
        <!-- Autosave Status -->
        <div id="autosave-status" class="flex items-center gap-2 text-sm text-gray-400">
            <span id="autosave-indicator" class="hidden">
                <span class="autosave-saving hidden flex items-center gap-1">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
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
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="autosave-time">Saved</span>
                </span>
            </span>
        </div>

        <button type="button" onclick="openSaveTemplateModal()" class="text-gray-400 hover:text-white p-2 rounded transition-colors" title="Save as Template">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
            </svg>
        </button>
        <button type="button" onclick="toggleSettingsPanel()" class="text-gray-400 hover:text-white p-2 rounded transition-colors" title="Page Settings">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>
        @if($page && $page->isPublished())
        <a href="{{ route('page.show', $page->slug) }}" target="_blank"
           class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white border border-gray-600 rounded-lg hover:border-gray-500 transition-colors">
            Preview
        </a>
        @endif
        <button type="button" onclick="savePage()" id="save-btn"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <!-- Panel Tabs -->
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
        <!-- Block Search -->
        <div id="block-search-wrapper" class="p-3 border-b border-gray-700">
            <div class="relative">
                <input type="text" id="block-search" placeholder="Search blocks..."
                       class="w-full bg-gray-700 border border-gray-600 text-white text-sm rounded-lg pl-9 pr-8 py-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <button type="button" id="block-search-clear" class="hidden absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Blocks Panel -->
        <div id="blocks-panel" class="flex-1 overflow-y-auto p-3"></div>
        <!-- Layers Panel -->
        <div id="layers-panel" class="flex-1 overflow-y-auto p-3 hidden"></div>
    </div>

    <!-- Center: Canvas -->
    <div class="flex-1 bg-gray-900 overflow-hidden">
        <div id="gjs" class="h-full"></div>
    </div>

    <!-- Right Panel: Styles & Settings -->
    <div class="w-72 bg-gray-800 border-l border-gray-700 flex flex-col">
        <!-- Panel Tabs -->
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
        <!-- Selector Panel -->
        <div id="selectors-panel" class="p-3 border-b border-gray-700"></div>
        <!-- Styles Panel -->
        <div id="styles-panel" class="flex-1 overflow-y-auto p-3"></div>
        <!-- Traits Panel -->
        <div id="traits-panel" class="flex-1 overflow-y-auto p-3 hidden"></div>
    </div>

    <!-- Settings Sidebar (Hidden by default) -->
    <div id="settings-panel" class="w-80 bg-gray-800 border-l border-gray-700 hidden flex-col">
        <div class="p-4 border-b border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-white">Page Settings</h3>
            <button type="button" onclick="toggleSettingsPanel()" class="text-gray-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            <!-- Publish Settings -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                <select id="page-status" class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2">
                    <option value="draft" {{ ($page->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ ($page->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Slug</label>
                <div class="flex items-center">
                    <span class="text-gray-500 mr-1">/p/</span>
                    <input type="text" id="page-slug" value="{{ $page->slug ?? '' }}"
                           class="flex-1 bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2"
                           placeholder="auto-generated">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Template</label>
                <select id="page-template" class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2">
                    <option value="default" {{ ($page->template ?? 'default') === 'default' ? 'selected' : '' }}>Default</option>
                    <option value="full-width" {{ ($page->template ?? '') === 'full-width' ? 'selected' : '' }}>Full Width</option>
                    <option value="landing" {{ ($page->template ?? '') === 'landing' ? 'selected' : '' }}>Landing Page</option>
                </select>
            </div>
            <!-- SEO -->
            <div class="pt-4 border-t border-gray-700">
                <h4 class="font-medium text-white mb-4">SEO Settings</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Meta Title</label>
                        <input type="text" id="page-meta-title" value="{{ $page->meta_title ?? '' }}"
                               class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2"
                               placeholder="Page title for SEO">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Meta Description</label>
                        <textarea id="page-meta-description" rows="3"
                                  class="w-full bg-gray-700 border-gray-600 text-white rounded-lg px-3 py-2"
                                  placeholder="Brief description for search engines">{{ $page->meta_description ?? '' }}</textarea>
                    </div>
                </div>
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

    /* GrapesJS Customizations */
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
    const pageId = {{ $page->id ?? 'null' }};
    const isEdit = pageId !== null;
    const projectData = @json($page->content ?? null);
    let allTemplates = [];
    let editor = null;
    let autosave = null;

    // Load templates on page load
    loadTemplates();

    // Initialize GrapesJS
    editor = window.initGrapesJS({
        projectData: projectData,
        assets: [],
    });

    // Initialize Autosave
    autosave = new window.GrapesJSAutosave(editor, {
        pageId: pageId || 'new',
        interval: 30000, // 30 seconds
        onStatusChange: updateAutosaveUI,
    });

    // Check for recovery data
    checkForRecovery();

    // Autosave status UI update function
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

    // Recovery functions
    function checkForRecovery() {
        if (autosave.hasRecoveryData()) {
            const meta = autosave.getRecoveryMeta();
            // Only show recovery if it's for the same page (or if creating new and recovery is from new)
            if (meta && (meta.pageId === (pageId || 'new'))) {
                const banner = document.getElementById('recovery-banner');
                const message = document.getElementById('recovery-message');
                const timeAgo = window.formatTimeAgo(new Date(meta.timestamp));
                message.textContent = `Unsaved changes found from ${timeAgo}${meta.title ? ` (${meta.title})` : ''}`;
                banner.classList.remove('hidden');
            }
        }
    }

    window.restoreRecovery = function() {
        if (autosave.restore()) {
            document.getElementById('recovery-banner').classList.add('hidden');
            // Show success toast
            showToast('Changes restored successfully', 'success');
        }
    };

    window.dismissRecovery = function() {
        autosave.clearRecoveryData();
        document.getElementById('recovery-banner').classList.add('hidden');
    };

    // Toast notification helper
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white z-50 transition-opacity duration-300 ${
            type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Make autosave accessible
    window.gjsAutosave = autosave;

    // Panel switching functions
    window.showLeftPanel = function(panel) {
        document.getElementById('blocks-panel').classList.toggle('hidden', panel !== 'blocks');
        document.getElementById('layers-panel').classList.toggle('hidden', panel !== 'layers');
        document.getElementById('block-search-wrapper').classList.toggle('hidden', panel !== 'blocks');
        document.getElementById('tab-blocks').classList.toggle('active', panel === 'blocks');
        document.getElementById('tab-layers').classList.toggle('active', panel === 'layers');
        // Clear search when switching panels
        if (panel !== 'blocks') {
            clearBlockSearch();
        }
    };

    // Block search functionality
    const blockSearchInput = document.getElementById('block-search');
    const blockSearchClear = document.getElementById('block-search-clear');

    blockSearchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        filterBlocks(query);
        blockSearchClear.classList.toggle('hidden', query.length === 0);
    });

    blockSearchClear.addEventListener('click', function() {
        clearBlockSearch();
    });

    function clearBlockSearch() {
        blockSearchInput.value = '';
        blockSearchClear.classList.add('hidden');
        filterBlocks('');
    }

    function filterBlocks(query) {
        const blocksPanel = document.getElementById('blocks-panel');
        const blocks = blocksPanel.querySelectorAll('.gjs-block');
        const categories = blocksPanel.querySelectorAll('.gjs-block-category');

        if (!query) {
            // Show all blocks and categories
            blocks.forEach(block => block.style.display = '');
            categories.forEach(cat => {
                cat.style.display = '';
                // Restore original open/closed state
                const title = cat.querySelector('.gjs-category-title');
                if (title && !title.classList.contains('gjs-category-open')) {
                    const content = cat.querySelector('.gjs-blocks-c');
                    if (content) content.style.display = 'none';
                }
            });
            return;
        }

        // Filter blocks by label
        blocks.forEach(block => {
            const label = block.querySelector('.gjs-block-label');
            const labelText = label ? label.textContent.toLowerCase() : '';
            const matches = labelText.includes(query);
            block.style.display = matches ? '' : 'none';
        });

        // Show/hide categories based on visible blocks
        categories.forEach(cat => {
            const visibleBlocks = cat.querySelectorAll('.gjs-block:not([style*="display: none"])');
            const hasVisible = visibleBlocks.length > 0;
            cat.style.display = hasVisible ? '' : 'none';

            // Expand categories with matches
            if (hasVisible) {
                const content = cat.querySelector('.gjs-blocks-c');
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
        const panel = document.getElementById('settings-panel');
        panel.classList.toggle('hidden');
        panel.classList.toggle('flex');
    };

    // Device switching
    window.setDevice = function(device) {
        editor.setDevice(device);
        document.querySelectorAll('.device-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById('btn-' + device.toLowerCase()).classList.add('active');
    };

    // Undo/Redo functions
    window.editorUndo = function() {
        if (editor) {
            editor.UndoManager.undo();
            updateUndoRedoState();
        }
    };

    window.editorRedo = function() {
        if (editor) {
            editor.UndoManager.redo();
            updateUndoRedoState();
        }
    };

    function updateUndoRedoState() {
        const um = editor.UndoManager;
        const undoBtn = document.getElementById('btn-undo');
        const redoBtn = document.getElementById('btn-redo');

        undoBtn.disabled = !um.hasUndo();
        redoBtn.disabled = !um.hasRedo();
    }

    // Update undo/redo state on changes
    editor.on('change:changesCount', updateUndoRedoState);

    // Keyboard shortcuts for undo/redo
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && !e.shiftKey && e.key === 'z') {
            e.preventDefault();
            editorUndo();
        }
        if ((e.ctrlKey || e.metaKey) && (e.shiftKey && e.key === 'z' || e.key === 'y')) {
            e.preventDefault();
            editorRedo();
        }
        // Ctrl+S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            savePage();
        }
    });

    // Initial state
    updateUndoRedoState();

    // Save page
    window.savePage = function() {
        const btn = document.getElementById('save-btn');
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...';
        btn.disabled = true;

        const data = window.getGrapesJSData();
        const formData = {
            title: document.getElementById('page-title').value,
            slug: document.getElementById('page-slug').value,
            status: document.getElementById('page-status').value,
            template: document.getElementById('page-template').value,
            meta_title: document.getElementById('page-meta-title').value,
            meta_description: document.getElementById('page-meta-description').value,
            content: JSON.stringify(data.projectData),
            html: data.html + '<style>' + data.css + '</style>',
        };

        const url = isEdit ? `/admin/pages/${pageId}` : '/admin/pages';
        const method = isEdit ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(formData),
        })
        .then(response => response.json())
        .then(result => {
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Saved!';
            setTimeout(() => {
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save';
                btn.disabled = false;
            }, 2000);

            // Clear autosave data after successful server save
            if (autosave) {
                autosave.onServerSave();
            }

            // Redirect to edit page if new
            if (!isEdit && result.page && result.page.id) {
                window.history.replaceState({}, '', `/admin/pages/${result.page.id}/edit`);
                // Update autosave key for the new page ID
                if (autosave) {
                    autosave.pageId = result.page.id;
                    autosave.storageKey = `gjs_autosave_${result.page.id}`;
                    autosave.metaKey = `gjs_autosave_meta_${result.page.id}`;
                }
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            btn.innerHTML = 'Error! Try again';
            btn.classList.add('bg-red-600');
            setTimeout(() => {
                btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save';
                btn.classList.remove('bg-red-600');
                btn.disabled = false;
            }, 3000);
        });
    };
// Template Modal Event Delegation
    document.addEventListener('click', function(e) {
        // Close template modal on backdrop click
        if (e.target.classList.contains('modal-backdrop')) {
            closeTemplateModal();
        }

        // Close template modal on X button click
        if (e.target.closest('.close-modal-btn')) {
            closeTemplateModal();
        }

        // Category filter buttons
        const catBtn = e.target.closest('.template-cat-btn');
        if (catBtn) {
            const category = catBtn.dataset.category;
            if (category) {
                filterTemplates(category);
            }
        }

        // Template card selection
        const templateCard = e.target.closest('.template-card');
        if (templateCard) {
            const templateId = templateCard.dataset.templateId;
            if (templateId) {
                selectTemplate(templateId === 'blank' ? 'blank' : parseInt(templateId));
            }
        }

        // Close save template modal on backdrop click
        if (e.target.classList.contains('save-modal-backdrop')) {
            closeSaveTemplateModal();
        }

        // Cancel save template button
        if (e.target.closest('.cancel-save-btn')) {
            closeSaveTemplateModal();
        }

        // Save template button
        if (e.target.id === 'save-template-btn' || e.target.closest('#save-template-btn')) {
            saveAsTemplate();
        }
    });

    // Template Functions
    async function loadTemplates() {
        try {
            const response = await fetch('/admin/templates', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            allTemplates = data.templates;
            renderTemplates('all');
        } catch (error) {
            console.error('Error loading templates:', error);
            document.getElementById('templates-loading').innerHTML = '<p class="text-gray-400">Failed to load templates</p>';
        }
    }

    function renderTemplates(category) {
        const grid = document.getElementById('templates-grid');
        const loading = document.getElementById('templates-loading');
        if (loading) loading.remove();

        // Keep the blank template card
        const blankCard = grid.querySelector('[data-template-id="blank"]');

        // Remove other template cards
        grid.querySelectorAll('.template-card:not([data-template-id="blank"])').forEach(el => el.remove());

        // Flatten templates from all categories
        let templates = [];
        Object.keys(allTemplates).forEach(cat => {
            if (category === 'all' || category === cat) {
                templates = templates.concat(allTemplates[cat] || []);
            }
        });

        // Render template cards
        templates.forEach(template => {
            const card = document.createElement('div');
            card.className = 'template-card cursor-pointer group';
            card.dataset.templateId = template.id;
            card.dataset.category = template.category;
            card.innerHTML = `
                <div class="aspect-[4/3] bg-gray-700 rounded-lg border-2 border-gray-600 overflow-hidden group-hover:border-blue-500 transition-colors">
                    ${template.thumbnail
                        ? `<img src="${template.thumbnail}" class="w-full h-full object-cover" />`
                        : `<div class="w-full h-full flex items-center justify-center">
                               <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                               </svg>
                           </div>`
                    }
                </div>
                <h3 class="mt-3 font-medium text-white">${template.name}</h3>
                <p class="text-sm text-gray-400">${template.description || template.category}</p>
                ${template.is_system ? '<span class="text-xs text-blue-400">System Template</span>' : ''}
            `;
            grid.appendChild(card);
        });
    }

    window.filterTemplates = function(category) {
        document.querySelectorAll('.template-cat-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.category === category);
        });
        renderTemplates(category);
    };

    window.selectTemplate = async function(templateId) {
        if (templateId === 'blank') {
            closeTemplateModal();
            return;
        }

        try {
            const response = await fetch(`/admin/templates/${templateId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.template && data.template.content) {
                editor.loadProjectData(data.template.content);
            }
            closeTemplateModal();
        } catch (error) {
            console.error('Error loading template:', error);
            alert('Failed to load template');
        }
    };

    window.closeTemplateModal = function() {
        document.getElementById('template-modal').classList.add('hidden');
    };

    window.openSaveTemplateModal = function() {
        document.getElementById('save-template-modal').classList.remove('hidden');
        document.getElementById('save-template-modal').classList.add('flex');
        document.getElementById('template-name').value = document.getElementById('page-title').value || '';
    };

    window.closeSaveTemplateModal = function() {
        document.getElementById('save-template-modal').classList.add('hidden');
        document.getElementById('save-template-modal').classList.remove('flex');
    };

    window.saveAsTemplate = async function() {
        const btn = document.getElementById('save-template-btn');
        btn.textContent = 'Capturing...';
        btn.disabled = true;

        try {
            // Capture thumbnail first
            const thumbnail = await window.captureCanvasThumbnail({
                maxWidth: 400,
                maxHeight: 300,
                quality: 0.7,
            });

            btn.textContent = 'Saving...';

            const data = window.getGrapesJSData();
            const formData = {
                name: document.getElementById('template-name').value,
                description: document.getElementById('template-description').value,
                category: document.getElementById('template-category').value,
                content: JSON.stringify(data.projectData),
                thumbnail: thumbnail, // Include captured thumbnail
            };

            const response = await fetch('/admin/templates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            const result = await response.json();
            if (result.success) {
                btn.textContent = 'Saved!';
                setTimeout(() => {
                    closeSaveTemplateModal();
                    btn.textContent = 'Save Template';
                    btn.disabled = false;
                    // Reload templates
                    loadTemplates();
                }, 1000);
            } else {
                throw new Error(result.message || 'Failed to save');
            }
        } catch (error) {
            console.error('Save template error:', error);
            btn.textContent = 'Error!';
            setTimeout(() => {
                btn.textContent = 'Save Template';
                btn.disabled = false;
            }, 2000);
        }
    };
});
</script>
@endpush
