/**
 * Global Colors panel for GrapesJS builder.
 * Brand color palette with one-click apply to selected component.
 */
export default function registerGlobalColors(editor) {
    const STORAGE_KEY = 'pp_brand_colors';

    const defaultColors = [
        { hex: '#9A7B4F', name: 'Gold' },
        { hex: '#1a1714', name: 'Dark' },
        { hex: '#f8f5f0', name: 'Cream' },
        { hex: '#A67B5B', name: 'Caramel' },
        { hex: '#C9A227', name: 'Accent' },
        { hex: '#10b981', name: 'Success' },
        { hex: '#ffffff', name: 'White' },
        { hex: '#000000', name: 'Black' },
    ];

    let colors = loadColors();
    let applyMode = 'color'; // color | background-color | border-color

    function loadColors() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            return saved ? JSON.parse(saved) : [...defaultColors];
        } catch { return [...defaultColors]; }
    }

    function saveColors() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(colors));
    }

    function render() {
        const container = document.getElementById('global-colors-panel');
        if (!container) return;

        container.innerHTML = `
            <div class="mb-2 flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Brand Colors</span>
                <select id="gc-mode" class="bg-gray-700 border-gray-600 text-gray-300 text-xs rounded px-1.5 py-0.5">
                    <option value="color"${applyMode === 'color' ? ' selected' : ''}>Text</option>
                    <option value="background-color"${applyMode === 'background-color' ? ' selected' : ''}>Background</option>
                    <option value="border-color"${applyMode === 'border-color' ? ' selected' : ''}>Border</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-1.5 mb-2" id="gc-swatches"></div>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-1.5 cursor-pointer text-xs text-gray-400 hover:text-white transition-colors">
                    <input type="color" id="gc-picker" value="#9A7B4F" class="w-5 h-5 rounded cursor-pointer border-0 bg-transparent p-0">
                    Add color
                </label>
                <button type="button" id="gc-reset" class="text-xs text-gray-500 hover:text-red-400 ml-auto transition-colors">Reset</button>
            </div>`;

        renderSwatches();
        container.querySelector('#gc-mode').addEventListener('change', e => { applyMode = e.target.value; });
        container.querySelector('#gc-picker').addEventListener('change', e => addColor(e.target.value));
        container.querySelector('#gc-reset').addEventListener('click', resetColors);
    }

    function renderSwatches() {
        const box = document.getElementById('gc-swatches');
        if (!box) return;
        box.innerHTML = '';

        colors.forEach((c, i) => {
            const swatch = document.createElement('div');
            swatch.className = 'group relative';
            swatch.innerHTML = `
                <button type="button" class="gc-swatch w-7 h-7 rounded border border-gray-600 hover:scale-110 transition-transform hover:border-white"
                        style="background:${c.hex}" title="${c.name} (${c.hex})" data-index="${i}"></button>
                <button type="button" class="gc-del absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-600 rounded-full text-white text-[8px] leading-none
                        hidden group-hover:flex items-center justify-center" data-del="${i}">&times;</button>`;
            box.appendChild(swatch);
        });

        box.querySelectorAll('.gc-swatch').forEach(btn => {
            btn.addEventListener('click', () => applyColor(colors[btn.dataset.index].hex));
        });
        box.querySelectorAll('.gc-del').forEach(btn => {
            btn.addEventListener('click', e => { e.stopPropagation(); removeColor(parseInt(btn.dataset.del)); });
        });
    }

    function applyColor(hex) {
        const selected = editor.getSelected();
        if (!selected) {
            window.showEditorToast?.('Select an element first', 'error');
            return;
        }
        selected.addStyle({ [applyMode]: hex });
        window.showEditorToast?.(`Applied ${hex}`, 'success');
    }

    function addColor(hex) {
        if (colors.some(c => c.hex.toLowerCase() === hex.toLowerCase())) return;
        colors.push({ hex, name: hex });
        saveColors();
        renderSwatches();
    }

    function removeColor(index) {
        colors.splice(index, 1);
        saveColors();
        renderSwatches();
    }

    function resetColors() {
        colors = [...defaultColors];
        saveColors();
        renderSwatches();
    }

    // Insert panel container above styles panel after editor loads
    editor.on('load', () => {
        const stylesPanel = document.getElementById('styles-panel');
        if (!stylesPanel) return;

        const panel = document.createElement('div');
        panel.id = 'global-colors-panel';
        panel.className = 'p-3 border-b border-gray-700';
        stylesPanel.parentNode.insertBefore(panel, stylesPanel);
        render();
    });
}
