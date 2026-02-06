/**
 * Unsplash stock photo integration for GrapesJS builder.
 * Opens a search modal, displays results, inserts photos into the canvas.
 * Backend proxy at /admin/unsplash/search keeps the API key server-side.
 */
export default function registerUnsplash(editor) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    let page = 1, query = '', pages = 0;

    // Build overlay
    const el = document.createElement('div');
    el.id = 'unsplash-modal';
    el.className = 'fixed inset-0 z-[9999] hidden';
    el.innerHTML = `
        <div class="absolute inset-0 bg-black/70" data-close></div>
        <div class="absolute inset-4 lg:inset-8 bg-gray-900 rounded-xl flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7.5 6.75V0h9v6.75h-9zm9 3.75H24V24H0V10.5h7.5v6.75h9V10.5z"/></svg>
                    Unsplash Photos
                </h2>
                <button data-close class="text-gray-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-3 border-b border-gray-700">
                <input id="us-input" type="text" placeholder="Search free high-resolution photos..."
                    class="w-full bg-gray-800 border border-gray-600 text-white rounded-lg px-4 py-2.5 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
            </div>
            <div id="us-results" class="flex-1 overflow-y-auto p-6">
                <p class="text-gray-400 text-center py-12">Search for photos to insert into your page</p>
            </div>
            <div id="us-footer" class="px-6 py-2 border-t border-gray-700 text-xs text-gray-500 text-center hidden">
                Photos provided by <a href="https://unsplash.com/?utm_source=pepprofesor&utm_medium=referral" target="_blank" rel="noopener" class="underline hover:text-gray-300">Unsplash</a>
            </div>
        </div>`;
    document.body.appendChild(el);

    // Close handlers
    el.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', close));
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !el.classList.contains('hidden')) close(); });

    // Debounced search
    const input = el.querySelector('#us-input');
    let timer;
    input.addEventListener('input', e => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            query = e.target.value.trim();
            page = 1;
            if (query.length >= 2) doSearch();
        }, 400);
    });

    async function doSearch() {
        const box = el.querySelector('#us-results');
        if (page === 1) box.innerHTML = '<p class="text-gray-400 text-center py-12">Searching...</p>';

        try {
            const r = await fetch(`/admin/unsplash/search?query=${encodeURIComponent(query)}&page=${page}`, {
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
            });
            if (!r.ok) {
                const err = await r.json().catch(() => ({}));
                box.innerHTML = `<p class="text-red-400 text-center py-12">${err.error || 'Search failed'}</p>`;
                return;
            }
            const data = await r.json();
            pages = data.total_pages;
            render(data.results, page > 1);
            el.querySelector('#us-footer').classList.remove('hidden');
        } catch {
            box.innerHTML = '<p class="text-red-400 text-center py-12">Network error</p>';
        }
    }

    function render(photos, append) {
        const box = el.querySelector('#us-results');
        if (!append) box.innerHTML = '';

        if (!photos.length && !append) {
            box.innerHTML = '<p class="text-gray-400 text-center py-12">No photos found. Try a different search.</p>';
            return;
        }

        let grid = box.querySelector('.us-grid');
        if (!grid) {
            grid = document.createElement('div');
            grid.className = 'us-grid grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3';
            box.appendChild(grid);
        }

        photos.forEach(p => {
            const card = document.createElement('div');
            card.className = 'group relative cursor-pointer rounded-lg overflow-hidden bg-gray-800 aspect-[4/3]';
            card.innerHTML = `
                <img src="${p.thumb}" alt="${escape(p.alt)}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                    <span class="text-white text-sm truncate">${escape(p.author)}</span>
                </div>`;
            card.addEventListener('click', () => insert(p));
            grid.appendChild(card);
        });

        box.querySelector('.us-more')?.remove();
        if (page < pages) {
            const btn = document.createElement('button');
            btn.className = 'us-more mt-4 mx-auto block px-6 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 text-sm';
            btn.textContent = 'Load More';
            btn.addEventListener('click', () => { page++; doSearch(); });
            box.appendChild(btn);
        }
    }

    function insert(photo) {
        const selected = editor.getSelected();
        const parent = selected || editor.getWrapper();
        const alt = photo.alt || 'Photo by ' + photo.author + ' on Unsplash';
        parent.append(`<img src="${photo.regular}" alt="${escape(alt)}" style="width:100%;height:auto;border-radius:8px;" loading="lazy" />`);

        // Unsplash API guideline: trigger download tracking
        if (photo.download_url) {
            fetch('/admin/unsplash/track-download', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ download_url: photo.download_url }),
            }).catch(() => {});
        }

        close();
        window.showEditorToast?.('Photo inserted!', 'success');
    }

    function escape(str) {
        const d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }

    function close() { el.classList.add('hidden'); }

    window.openUnsplashModal = function() {
        el.classList.remove('hidden');
        input.focus();
    };
}
