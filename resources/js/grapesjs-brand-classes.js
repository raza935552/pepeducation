/**
 * Brand Classes panel for GrapesJS builder.
 * Categorised, one-click class toggles with search and active-state highlighting.
 * Renders into #brand-classes-panel (its own tab in the right sidebar).
 */
export default function registerBrandClasses(editor) {
    const CATEGORIES = [
        {
            name: 'Buttons',
            classes: [
                'btn', 'btn-primary', 'btn-gold', 'btn-secondary',
                'btn-outline', 'btn-ghost', 'btn-lg', 'btn-sm', 'btn-pill',
                'btn-caramel', 'btn-dark', 'btn-white', 'btn-block',
            ],
        },
        {
            name: 'Cards',
            classes: [
                'card', 'card-compact', 'card-cream',
                'card-dark', 'card-bordered', 'card-hover',
            ],
        },
        {
            name: 'Badges',
            classes: [
                'badge', 'badge-gold', 'badge-cream', 'badge-success',
                'badge-warning', 'badge-error', 'badge-dark', 'badge-outline',
            ],
        },
        {
            name: 'Inputs',
            classes: [
                'input', 'input-pill', 'input-sm', 'input-lg',
            ],
        },
        {
            name: 'Typography',
            classes: [
                'section-heading', 'section-heading-lg', 'text-gradient-gold',
                'text-xs', 'text-sm', 'text-base', 'text-lg', 'text-xl',
                'text-2xl', 'text-3xl', 'text-4xl', 'text-5xl',
                'font-normal', 'font-medium', 'font-semibold', 'font-bold', 'font-extrabold',
                'text-center', 'text-left', 'text-right',
                'uppercase', 'lowercase', 'capitalize', 'normal-case',
                'italic', 'not-italic',
                'underline', 'line-through', 'no-underline',
                'leading-tight', 'leading-snug', 'leading-normal', 'leading-relaxed', 'leading-loose',
                'tracking-tight', 'tracking-normal', 'tracking-wide', 'tracking-wider', 'tracking-widest',
                'whitespace-nowrap', 'whitespace-normal',
                'truncate',
                'list-disc', 'list-decimal', 'list-none', 'list-inside',
            ],
        },
        {
            name: 'Layout',
            classes: [
                'container-narrow', 'container-wide',
                'flex', 'inline-flex', 'flex-col', 'flex-row', 'flex-wrap', 'flex-nowrap',
                'flex-1', 'flex-auto', 'flex-none',
                'items-center', 'items-start', 'items-end', 'items-stretch',
                'justify-center', 'justify-between', 'justify-start', 'justify-end', 'justify-around', 'justify-evenly',
                'self-auto', 'self-start', 'self-center', 'self-end',
                'gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-6', 'gap-8', 'gap-10', 'gap-12',
                'grid', 'grid-cols-1', 'grid-cols-2', 'grid-cols-3', 'grid-cols-4', 'grid-cols-5', 'grid-cols-6',
                'col-span-2', 'col-span-3', 'col-span-full',
                'block', 'inline-block', 'inline', 'hidden',
                'w-full', 'w-auto', 'w-1/2', 'w-1/3', 'w-2/3', 'w-1/4', 'w-3/4',
                'h-auto', 'h-full', 'h-screen',
                'min-h-screen', 'min-h-0',
                'max-w-sm', 'max-w-md', 'max-w-lg', 'max-w-xl', 'max-w-2xl', 'max-w-4xl', 'max-w-6xl', 'max-w-full', 'max-w-none',
                'mx-auto',
                'object-cover', 'object-contain', 'object-center',
                'aspect-video', 'aspect-square',
            ],
        },
        {
            name: 'Text Colors',
            classes: [
                'text-gold', 'text-gold-400', 'text-caramel',
                'text-cream-500', 'text-brown-900',
                'text-white', 'text-black',
                'text-gray-400', 'text-gray-500', 'text-gray-600', 'text-gray-700', 'text-gray-900',
                'text-green-600', 'text-green-700',
                'text-red-500', 'text-red-600',
                'text-blue-600',
                'text-amber-600',
            ],
        },
        {
            name: 'Backgrounds',
            classes: [
                'bg-cream-50', 'bg-cream-100', 'bg-cream-200',
                'bg-gold-500', 'bg-gold-400',
                'bg-caramel-500',
                'bg-brown-800', 'bg-brown-900',
                'bg-white', 'bg-black',
                'bg-gray-50', 'bg-gray-100', 'bg-gray-200', 'bg-gray-800', 'bg-gray-900',
                'bg-green-50', 'bg-green-100',
                'bg-red-50', 'bg-red-100',
                'bg-blue-50', 'bg-blue-100',
                'bg-amber-50', 'bg-amber-100',
                'bg-transparent',
                'bg-gradient-gold', 'bg-gradient-dark',
                'glass',
            ],
        },
        {
            name: 'Spacing',
            classes: [
                'mt-0', 'mt-1', 'mt-2', 'mt-3', 'mt-4', 'mt-6', 'mt-8', 'mt-10', 'mt-12', 'mt-16', 'mt-auto',
                'mb-0', 'mb-1', 'mb-2', 'mb-3', 'mb-4', 'mb-6', 'mb-8', 'mb-10', 'mb-12', 'mb-16',
                'ml-auto', 'mr-auto',
                'py-0', 'py-1', 'py-2', 'py-3', 'py-4', 'py-6', 'py-8', 'py-10', 'py-12', 'py-16', 'py-20', 'py-24',
                'px-0', 'px-2', 'px-3', 'px-4', 'px-6', 'px-8', 'px-10', 'px-12',
                'p-0', 'p-2', 'p-3', 'p-4', 'p-6', 'p-8', 'p-10', 'p-12',
                'space-y-2', 'space-y-4', 'space-y-6', 'space-y-8',
                'space-x-2', 'space-x-4', 'space-x-6',
            ],
        },
        {
            name: 'Borders & Radius',
            classes: [
                'rounded-none', 'rounded', 'rounded-md', 'rounded-lg', 'rounded-xl', 'rounded-2xl', 'rounded-3xl', 'rounded-full',
                'border', 'border-0', 'border-2', 'border-4',
                'border-t', 'border-b', 'border-l', 'border-r',
                'border-cream', 'border-gold', 'border-gray-200', 'border-gray-300', 'border-white',
                'divide-y', 'divide-cream',
            ],
        },
        {
            name: 'Shadows & Effects',
            classes: [
                'shadow-none', 'shadow-sm', 'shadow', 'shadow-md', 'shadow-lg', 'shadow-xl', 'shadow-2xl',
                'glow-gold', 'glow-caramel',
                'opacity-0', 'opacity-25', 'opacity-50', 'opacity-75', 'opacity-100',
                'transition', 'transition-all', 'transition-colors',
                'duration-150', 'duration-200', 'duration-300', 'duration-500',
                'hover-scale', 'hover-lift',
                'overflow-hidden', 'overflow-auto', 'overflow-x-auto', 'overflow-y-auto',
                'relative', 'absolute', 'fixed', 'sticky',
                'inset-0', 'top-0', 'bottom-0', 'left-0', 'right-0',
                'z-0', 'z-10', 'z-20', 'z-30', 'z-50',
                'cursor-pointer',
            ],
        },
    ];

    let searchQuery = '';
    let openCategories = {};

    function getSelectedClasses() {
        const selected = editor.getSelected();
        if (!selected) return [];
        return selected.getClasses();
    }

    function toggleClass(cls) {
        const selected = editor.getSelected();
        if (!selected) {
            window.showEditorToast?.('Select an element first', 'error');
            return;
        }
        const classes = selected.getClasses();
        if (classes.includes(cls)) {
            selected.removeClass(cls);
        } else {
            selected.addClass(cls);
        }
        render();
    }

    function render() {
        const container = document.getElementById('brand-classes-panel');
        if (!container) return;

        const activeClasses = getSelectedClasses();
        const query = searchQuery.toLowerCase();

        // Count active classes for header
        const activeCount = activeClasses.length;

        let html = `
            <div class="mb-3 flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Brand Classes</span>
                ${activeCount > 0 ? `<span class="text-[10px] bg-blue-600 text-white px-1.5 py-0.5 rounded-full">${activeCount} active</span>` : ''}
            </div>
            <div class="mb-3">
                <div class="relative">
                    <input type="text" id="bc-search" placeholder="Search classes..."
                           value="${escapeAttr(searchQuery)}"
                           class="w-full bg-gray-700 border border-gray-600 text-white text-xs rounded-lg pl-8 pr-8 py-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400">
                    <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    ${searchQuery ? '<button type="button" id="bc-search-clear" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>' : ''}
                </div>
            </div>`;

        // Active classes summary
        if (activeCount > 0 && !query) {
            html += `<div class="mb-3 pb-2 border-b border-gray-700">
                <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-1.5">Applied</div>
                <div class="flex flex-wrap gap-1">`;
            for (const cls of activeClasses) {
                html += `<button type="button"
                    class="bc-cls px-2 py-0.5 rounded text-[11px] bg-blue-600 text-white hover:bg-red-500 transition-colors"
                    data-cls="${escapeAttr(cls)}" title="Click to remove">.${cls}</button>`;
            }
            html += '</div></div>';
        }

        for (const cat of CATEGORIES) {
            const filtered = cat.classes.filter(c => !query || c.includes(query));
            if (filtered.length === 0) continue;

            // Count active in this category
            const catActiveCount = filtered.filter(c => activeClasses.includes(c)).length;

            const isOpen = query || openCategories[cat.name];
            html += `
                <div class="bc-category mb-0.5">
                    <button type="button" class="bc-cat-toggle w-full flex items-center justify-between py-1.5 px-1 text-xs font-semibold text-gray-400 uppercase tracking-wider hover:text-gray-200 hover:bg-gray-700/50 rounded transition-colors"
                            data-cat="${escapeAttr(cat.name)}">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3 h-3 transition-transform ${isOpen ? 'rotate-90' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            ${cat.name}
                        </span>
                        <span class="flex items-center gap-1">
                            ${catActiveCount > 0 ? `<span class="text-[9px] bg-blue-600/50 text-blue-300 px-1 py-px rounded">${catActiveCount}</span>` : ''}
                            <span class="text-[9px] text-gray-600">${filtered.length}</span>
                        </span>
                    </button>
                    <div class="bc-class-list flex flex-wrap gap-1 pb-2 pl-1 ${isOpen ? '' : 'hidden'}">`;

            for (const cls of filtered) {
                const isActive = activeClasses.includes(cls);
                html += `<button type="button"
                    class="bc-cls px-2 py-0.5 rounded text-[11px] transition-colors ${
                        isActive
                            ? 'bg-blue-600 text-white hover:bg-blue-700'
                            : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white'
                    }" data-cls="${escapeAttr(cls)}">.${cls}</button>`;
            }
            html += '</div></div>';
        }

        container.innerHTML = html;

        // Bind search — preserve focus and cursor
        const searchInput = container.querySelector('#bc-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value;
                const pos = e.target.selectionStart;
                render();
                // Restore focus and cursor
                const el = document.getElementById('bc-search');
                if (el) { el.focus(); el.setSelectionRange(pos, pos); }
            });
        }

        const clearBtn = container.querySelector('#bc-search-clear');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                searchQuery = '';
                render();
            });
        }

        // Bind category toggles
        container.querySelectorAll('.bc-cat-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const cat = btn.dataset.cat;
                openCategories[cat] = !openCategories[cat];
                render();
            });
        });

        // Bind class buttons
        container.querySelectorAll('.bc-cls').forEach(btn => {
            btn.addEventListener('click', () => {
                toggleClass(btn.dataset.cls);
            });
        });
    }

    function escapeAttr(str) {
        return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    // Re-render when selection changes
    editor.on('component:selected', () => render());
    editor.on('component:deselected', () => render());

    // Render into existing #brand-classes-panel div (defined in builder.blade.php)
    editor.on('load', () => {
        render();
    });
}
