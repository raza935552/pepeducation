/**
 * Import/Export for GrapesJS pages.
 * Export as standalone HTML file. Import HTML into canvas.
 */
export default function registerImportExport(editor) {
    // ===== EXPORT =====
    editor.Commands.add('export-html', {
        run(editor) {
            const html = editor.getHtml();
            const css = editor.getCss();
            const title = document.getElementById('page-title')?.value || 'Page Export';

            const fullHtml = `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>${title}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: system-ui, -apple-system, sans-serif; }
${css}
</style>
</head>
<body>
${html}
</body>
</html>`;

            const blob = new Blob([fullHtml], { type: 'text/html' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${title.replace(/[^a-z0-9]/gi, '-').toLowerCase()}.html`;
            a.click();
            URL.revokeObjectURL(url);
            window.showEditorToast?.('Page exported as HTML', 'success');
        },
    });

    // ===== IMPORT =====
    editor.Commands.add('import-html', {
        run(editor) {
            if (document.getElementById('pp-import-modal')) return;

            const modal = document.createElement('div');
            modal.id = 'pp-import-modal';
            modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:9999;display:flex;align-items:center;justify-content:center;';

            modal.innerHTML = `
                <div style="background:#1f2937;border-radius:16px;padding:30px;width:600px;max-width:90vw;max-height:80vh;overflow:auto;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="color:#fff;font-size:18px;font-weight:600;">Import HTML</h3>
                        <button id="pp-import-close" style="background:none;border:none;color:#9ca3af;font-size:24px;cursor:pointer;">&times;</button>
                    </div>
                    <div style="margin-bottom:16px;">
                        <label style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#374151;color:#e5e7eb;border-radius:8px;cursor:pointer;font-size:14px;">
                            Upload HTML file
                            <input type="file" accept=".html,.htm" id="pp-import-file" style="display:none;" />
                        </label>
                    </div>
                    <textarea id="pp-import-code" placeholder="Or paste your HTML code here..." style="width:100%;height:200px;background:#374151;color:#e5e7eb;border:1px solid #4b5563;border-radius:8px;padding:12px;font-family:monospace;font-size:13px;resize:vertical;"></textarea>
                    <div style="display:flex;gap:10px;margin-top:16px;justify-content:flex-end;">
                        <button id="pp-import-cancel" style="padding:10px 20px;background:#374151;color:#e5e7eb;border:none;border-radius:8px;cursor:pointer;">Cancel</button>
                        <button id="pp-import-apply" style="padding:10px 20px;background:#9A7B4F;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;">Import</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            modal.querySelector('#pp-import-close').addEventListener('click', () => modal.remove());
            modal.querySelector('#pp-import-cancel').addEventListener('click', () => modal.remove());
            modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });

            // File upload handler
            modal.querySelector('#pp-import-file').addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (ev) => {
                    modal.querySelector('#pp-import-code').value = ev.target.result;
                };
                reader.readAsText(file);
            });

            // Apply import
            modal.querySelector('#pp-import-apply').addEventListener('click', () => {
                const code = modal.querySelector('#pp-import-code').value.trim();
                if (!code) { window.showEditorToast?.('No HTML to import', 'error'); return; }

                // Extract body content if full HTML document
                let bodyContent = code;
                const bodyMatch = code.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
                if (bodyMatch) bodyContent = bodyMatch[1];

                // Extract style if present
                let styleContent = '';
                const styleMatch = code.match(/<style[^>]*>([\s\S]*?)<\/style>/gi);
                if (styleMatch) {
                    styleContent = styleMatch.map(s => s.replace(/<\/?style[^>]*>/gi, '')).join('\n');
                }

                editor.setComponents(bodyContent);
                if (styleContent) editor.setStyle(styleContent);

                modal.remove();
                window.showEditorToast?.('HTML imported successfully', 'success');
            });
        },
    });

    // Expose for toolbar buttons
    window.exportPageHtml = function() { editor.runCommand('export-html'); };
    window.importPageHtml = function() { editor.runCommand('import-html'); };
}
