/**
 * Media Library for GrapesJS builder.
 * Browse, upload, delete, and insert previously uploaded images.
 */
export default function registerMediaLibrary(editor) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    const el = document.createElement('div');
    el.id = 'media-modal';
    el.className = 'fixed inset-0 z-[9999] hidden';
    el.innerHTML = `
        <div class="absolute inset-0 bg-black/70" data-close></div>
        <div class="absolute inset-4 lg:inset-8 bg-gray-900 rounded-xl flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Media Library
                </h2>
                <div class="flex items-center gap-3">
                    <label class="cursor-pointer px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Upload
                        <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden" id="ml-upload">
                    </label>
                    <button data-close class="text-gray-400 hover:text-white p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div id="ml-results" class="flex-1 overflow-y-auto p-6">
                <p class="text-gray-400 text-center py-12">Loading...</p>
            </div>
            <div class="px-6 py-2 border-t border-gray-700 text-xs text-gray-500 text-center">
                Click an image to insert it into your page
            </div>
        </div>`;
    document.body.appendChild(el);

    el.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', close));
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !el.classList.contains('hidden')) close(); });

    // Upload handler
    el.querySelector('#ml-upload').addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const fd = new FormData();
        fd.append('image', file);

        try {
            const r = await fetch('/admin/pages/upload-image', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf },
                body: fd,
            });
            if (r.ok) {
                window.showEditorToast?.('Image uploaded!', 'success');
                loadMedia();
            } else {
                const err = await r.json().catch(() => ({}));
                window.showEditorToast?.(err?.errors?.image?.[0] || 'Upload failed (max 5MB, JPG/PNG/WebP/GIF)', 'error');
            }
        } catch {
            window.showEditorToast?.('Upload failed', 'error');
        }
        e.target.value = '';
    });

    async function loadMedia() {
        const box = el.querySelector('#ml-results');
        box.innerHTML = '<p class="text-gray-400 text-center py-12">Loading...</p>';

        try {
            const r = await fetch('/admin/media', { headers: { Accept: 'application/json' } });
            const data = await r.json();
            renderMedia(data.media || []);
        } catch {
            box.innerHTML = '<p class="text-red-400 text-center py-12">Failed to load media</p>';
        }
    }

    function renderMedia(items) {
        const box = el.querySelector('#ml-results');

        if (!items.length) {
            box.innerHTML = `
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-400 text-lg">No images yet</p>
                    <p class="text-gray-500 text-sm mt-1">Upload images using the button above</p>
                </div>`;
            return;
        }

        const grid = document.createElement('div');
        grid.className = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3';

        items.forEach(item => {
            const card = document.createElement('div');
            card.className = 'group relative rounded-lg overflow-hidden bg-gray-800 aspect-square';
            card.innerHTML = `
                <img src="${item.url}" alt="${esc(item.name)}" loading="lazy" class="w-full h-full object-cover cursor-pointer group-hover:scale-105 transition-transform duration-200">
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex flex-col justify-between p-2 pointer-events-none">
                    <div class="flex justify-end pointer-events-auto opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="ml-del p-1.5 bg-red-600 hover:bg-red-700 rounded text-white" title="Delete">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        <p class="text-white text-xs truncate">${esc(item.name)}</p>
                        <p class="text-gray-300 text-xs">${fmtSize(item.size)}</p>
                    </div>
                </div>`;

            card.querySelector('img').addEventListener('click', () => insertImage(item));
            card.querySelector('.ml-del').addEventListener('click', e => { e.stopPropagation(); deleteImage(item, card); });
            grid.appendChild(card);
        });

        box.innerHTML = '';
        box.appendChild(grid);
    }

    function insertImage(item) {
        const parent = editor.getSelected() || editor.getWrapper();
        parent.append(`<img src="${item.url}" alt="${esc(item.name)}" style="width:100%;height:auto;" loading="lazy" />`);
        close();
        window.showEditorToast?.('Image inserted!', 'success');
    }

    async function deleteImage(item, card) {
        if (!confirm('Delete this image permanently?')) return;
        try {
            const r = await fetch('/admin/media', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ path: item.path }),
            });
            if (r.ok) {
                card.remove();
                window.showEditorToast?.('Image deleted', 'success');
                if (!el.querySelector('#ml-results .grid')?.children.length) loadMedia();
            }
        } catch { window.showEditorToast?.('Delete failed', 'error'); }
    }

    function fmtSize(b) {
        if (b < 1024) return b + ' B';
        if (b < 1048576) return (b / 1024).toFixed(1) + ' KB';
        return (b / 1048576).toFixed(1) + ' MB';
    }

    function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    function close() { el.classList.add('hidden'); }

    window.openMediaLibrary = function() {
        el.classList.remove('hidden');
        loadMedia();
    };
}
