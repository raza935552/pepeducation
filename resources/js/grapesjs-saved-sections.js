/**
 * Saved Sections plugin for GrapesJS.
 * Save selected components as reusable sections, load from panel.
 */
export default function registerSavedSections(editor) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    // Add "Save as Section" command to right-click context menu
    editor.on('component:selected', (component) => {
        const toolbar = component.get('toolbar') || [];
        const exists = toolbar.some(t => t.id === 'save-section');
        if (!exists) {
            toolbar.push({
                id: 'save-section',
                label: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>',
                command: 'save-section',
                attributes: { title: 'Save as Reusable Section' },
            });
            component.set('toolbar', toolbar);
        }
    });

    editor.Commands.add('save-section', {
        run(ed) {
            const selected = ed.getSelected();
            if (!selected) return;

            const name = prompt('Section name:');
            if (!name || !name.trim()) return;

            const html = selected.toHTML();
            const css = ed.CodeManager?.getCode(selected, 'css')?.css || '';
            const content = { html, css };

            fetch('/admin/sections', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name: name.trim(), content }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.showEditorToast?.('Section saved!', 'success');
                }
            })
            .catch(() => window.showEditorToast?.('Save failed', 'error'));
        },
    });

    // Expose function to open sections panel
    window.openSavedSections = async function() {
        try {
            const r = await fetch('/admin/sections', { headers: { Accept: 'application/json' } });
            const data = await r.json();
            showSectionsModal(data.sections || []);
        } catch {
            window.showEditorToast?.('Failed to load sections', 'error');
        }
    };

    function showSectionsModal(sections) {
        let modal = document.getElementById('sections-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'sections-modal';
            modal.className = 'fixed inset-0 z-[9999] hidden';
            document.body.appendChild(modal);
        }

        const list = sections.length ? sections.map(s => `
            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                <div class="flex-1 cursor-pointer" data-section-id="${s.id}">
                    <p class="text-white font-medium text-sm">${esc(s.name)}</p>
                    <p class="text-gray-400 text-xs">${s.category} &middot; ${new Date(s.created_at).toLocaleDateString()}</p>
                </div>
                <button data-section-del="${s.id}" class="p-1.5 text-gray-400 hover:text-red-400 transition-colors" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>`).join('')
            : '<p class="text-gray-400 text-center py-8">No saved sections yet. Select a component and click the save icon in its toolbar.</p>';

        modal.innerHTML = `
            <div class="absolute inset-0 bg-black/70" data-close></div>
            <div class="absolute inset-y-8 right-4 w-80 bg-gray-900 rounded-xl flex flex-col overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
                    <h3 class="text-white font-semibold">Saved Sections</h3>
                    <button data-close class="text-gray-400 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-2">${list}</div>
            </div>`;

        modal.classList.remove('hidden');

        modal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => modal.classList.add('hidden')));
        modal.querySelectorAll('[data-section-id]').forEach(el => el.addEventListener('click', () => loadSection(el.dataset.sectionId, modal)));
        modal.querySelectorAll('[data-section-del]').forEach(el => el.addEventListener('click', () => deleteSection(el.dataset.sectionDel, modal)));
    }

    async function loadSection(id, modal) {
        try {
            const r = await fetch(`/admin/sections/${id}`, { headers: { Accept: 'application/json' } });
            const data = await r.json();
            if (data.section?.content?.html) {
                const parent = editor.getSelected() || editor.getWrapper();
                parent.append(data.section.content.html);
                if (data.section.content.css) {
                    const existing = editor.getCss() || '';
                    editor.setStyle(existing + '\n' + data.section.content.css);
                }
                window.showEditorToast?.('Section inserted!', 'success');
                modal.classList.add('hidden');
            }
        } catch {
            window.showEditorToast?.('Failed to load section', 'error');
        }
    }

    async function deleteSection(id, modal) {
        if (!confirm('Delete this section?')) return;
        try {
            await fetch(`/admin/sections/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            });
            window.openSavedSections();
        } catch {
            window.showEditorToast?.('Delete failed', 'error');
        }
    }

    function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
}
