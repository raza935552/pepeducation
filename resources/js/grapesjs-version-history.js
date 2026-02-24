/**
 * Version History panel for GrapesJS builder.
 * Browse and restore previous page versions from a slide-out panel.
 */
export default function registerVersionHistory(editor) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    window.openVersionHistory = async function() {
        const pageId = getPageId();
        if (!pageId) {
            window.showEditorToast?.('Save the page first to track versions', 'error');
            return;
        }

        let modal = document.getElementById('version-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'version-modal';
            modal.className = 'fixed inset-0 z-[9999] hidden';
            document.body.appendChild(modal);
        }

        modal.innerHTML = `
            <div class="absolute inset-0 bg-black/70" data-close></div>
            <div class="absolute inset-y-8 right-4 w-80 bg-gray-900 rounded-xl flex flex-col overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
                    <h3 class="text-white font-semibold">Version History</h3>
                    <button data-close class="text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="version-list" class="flex-1 overflow-y-auto p-4">
                    <p class="text-gray-400 text-center py-8">Loading...</p>
                </div>
            </div>`;
        modal.classList.remove('hidden');
        modal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => modal.classList.add('hidden')));

        try {
            const r = await fetch(`${getBaseUrl()}/${pageId}/versions`, { headers: { Accept: 'application/json' } });
            if (!r.ok) throw new Error(r.status);
            const data = await r.json();
            renderVersions(data.versions || [], pageId, modal);
        } catch {
            document.getElementById('version-list').innerHTML = '<p class="text-red-400 text-center py-8">Failed to load versions</p>';
        }
    };

    function renderVersions(versions, pageId, modal) {
        const box = document.getElementById('version-list');
        if (!versions.length) {
            box.innerHTML = '<p class="text-gray-400 text-center py-8">No versions yet. Versions are created each time you save.</p>';
            return;
        }

        box.innerHTML = '<div class="space-y-2">' + versions.map(v => `
            <div class="p-3 bg-gray-700 rounded-lg">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-white font-medium text-sm">v${v.version}</span>
                    <span class="text-gray-400 text-xs">${timeAgo(v.created_at)}</span>
                </div>
                <p class="text-gray-300 text-xs truncate mb-2">${esc(v.title)}</p>
                <button data-restore="${v.id}" class="text-xs text-blue-400 hover:text-blue-300 font-medium transition-colors">
                    Restore this version
                </button>
            </div>`).join('') + '</div>';

        box.querySelectorAll('[data-restore]').forEach(btn => {
            btn.addEventListener('click', () => restoreVersion(pageId, btn.dataset.restore, modal));
        });
    }

    async function restoreVersion(pageId, versionId, modal) {
        if (!confirm('Restore this version? Your current work will be saved as a new version first.')) return;
        try {
            const r = await fetch(`${getBaseUrl()}/${pageId}/versions/${versionId}/restore`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            });
            if (!r.ok) throw new Error(r.status);
            const data = await r.json();
            if (data.success) {
                window.showEditorToast?.(data.message, 'success');
                modal.classList.add('hidden');
                location.reload();
            }
        } catch {
            window.showEditorToast?.('Restore failed', 'error');
        }
    }

    function getPageId() {
        // Support both pages (numeric ID) and blog posts (slug)
        const pageMatch = location.pathname.match(/\/admin\/pages\/(\d+)\/edit/);
        if (pageMatch) return pageMatch[1];
        const blogMatch = location.pathname.match(/\/admin\/blog-posts\/([^/]+)\/edit/);
        if (blogMatch) return blogMatch[1];
        return null;
    }

    function getBaseUrl() {
        if (location.pathname.includes('/admin/blog-posts/')) return '/admin/blog-posts';
        return '/admin/pages';
    }

    function timeAgo(date) {
        const s = Math.floor((Date.now() - new Date(date).getTime()) / 1000);
        if (s < 60) return 'just now';
        if (s < 3600) return Math.floor(s / 60) + 'm ago';
        if (s < 86400) return Math.floor(s / 3600) + 'h ago';
        return Math.floor(s / 86400) + 'd ago';
    }

    function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
}
