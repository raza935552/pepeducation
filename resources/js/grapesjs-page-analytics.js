/**
 * Page Analytics panel for GrapesJS builder.
 * Shows page views, unique visitors, avg time, and daily chart.
 */
export default function registerPageAnalytics(editor) {
    window.openPageAnalytics = async function() {
        const pageId = getPageId();
        if (!pageId) {
            window.showEditorToast?.('Save the page first to see analytics', 'error');
            return;
        }

        let modal = document.getElementById('analytics-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'analytics-modal';
            modal.className = 'fixed inset-0 z-[9999] hidden';
            document.body.appendChild(modal);
        }

        modal.innerHTML = `
            <div class="absolute inset-0 bg-black/70" data-close></div>
            <div class="absolute inset-y-8 right-4 w-96 bg-gray-900 rounded-xl flex flex-col overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
                    <h3 class="text-white font-semibold">Page Analytics</h3>
                    <button data-close class="text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="analytics-content" class="flex-1 overflow-y-auto p-4">
                    <p class="text-gray-400 text-center py-8">Loading...</p>
                </div>
            </div>`;
        modal.classList.remove('hidden');
        modal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => modal.classList.add('hidden')));

        try {
            const r = await fetch(`/admin/pages/${pageId}/analytics`, { headers: { Accept: 'application/json' } });
            if (!r.ok) throw new Error(r.status);
            const data = await r.json();
            renderAnalytics(data);
        } catch {
            document.getElementById('analytics-content').innerHTML = '<p class="text-red-400 text-center py-8">Failed to load analytics</p>';
        }
    };

    function renderAnalytics(data) {
        const box = document.getElementById('analytics-content');
        const stats = [
            { label: 'Total Views', value: data.views, icon: '👁' },
            { label: 'Unique Visitors', value: data.unique_visitors, icon: '👤' },
            { label: 'Avg Time (sec)', value: data.avg_time, icon: '⏱' },
            { label: 'Avg Scroll %', value: data.avg_scroll + '%', icon: '📊' },
        ];

        const grid = stats.map(s => `
            <div class="bg-gray-700 rounded-lg p-4 text-center">
                <div class="text-2xl mb-1">${s.icon}</div>
                <div class="text-2xl font-bold text-white">${s.value}</div>
                <div class="text-xs text-gray-400 mt-1">${s.label}</div>
            </div>`).join('');

        // Daily chart (simple bar chart)
        const daily = data.daily || {};
        const dates = Object.keys(daily);
        const maxViews = Math.max(1, ...Object.values(daily));
        const bars = dates.length ? dates.map(d => {
            const v = daily[d];
            const pct = Math.round((v / maxViews) * 100);
            return `<div class="flex items-end gap-1" style="flex:1;min-width:30px;">
                <div class="w-full flex flex-col items-center">
                    <span class="text-xs text-gray-400 mb-1">${v}</span>
                    <div class="w-full bg-blue-500 rounded-t" style="height:${Math.max(4, pct)}px;"></div>
                    <span class="text-[10px] text-gray-500 mt-1">${d.slice(5)}</span>
                </div>
            </div>`;
        }).join('') : '<p class="text-gray-500 text-xs text-center py-4">No data for last 7 days</p>';

        // Variant comparison
        let variantHtml = '';
        if (data.variants?.length > 1) {
            const totalViews = data.variants.reduce((s, v) => s + v.views, 0) || 1;
            variantHtml = `
                <div class="mt-6 pt-4 border-t border-gray-700">
                    <h4 class="text-white font-semibold text-sm mb-3">A/B Variant Comparison</h4>
                    <div class="space-y-2">${data.variants.map(v => `
                        <div class="bg-gray-700 rounded-lg p-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-300 truncate">${esc(v.title)}</span>
                                <span class="text-white font-bold">${v.views} views</span>
                            </div>
                            <div class="w-full bg-gray-600 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width:${Math.round((v.views / totalViews) * 100)}%"></div>
                            </div>
                            <span class="text-xs text-gray-400">Weight: ${v.weight}%</span>
                        </div>`).join('')}
                    </div>
                </div>`;
        }

        box.innerHTML = `
            <div class="grid grid-cols-2 gap-3 mb-6">${grid}</div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-3">Last 7 Days</h4>
                <div class="flex items-end gap-1" style="height:100px;">${bars}</div>
            </div>
            ${variantHtml}`;
    }

    function getPageId() {
        const m = location.pathname.match(/\/admin\/pages\/(\d+)\/edit/);
        return m ? m[1] : null;
    }

    function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
}
