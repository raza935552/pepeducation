/**
 * Mobile Preview mode for GrapesJS.
 * Adds a toolbar command to preview the page in a phone frame overlay.
 */
export default function registerMobilePreview(editor) {
    editor.Commands.add('mobile-preview', {
        run(editor) {
            if (document.getElementById('pp-mobile-preview')) return;

            const html = editor.getHtml();
            const css = editor.getCss();

            const overlay = document.createElement('div');
            overlay.id = 'pp-mobile-preview';
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.8);z-index:9999;display:flex;align-items:center;justify-content:center;';

            const phone = document.createElement('div');
            phone.style.cssText = 'width:375px;height:667px;background:#000;border-radius:40px;padding:20px 10px;box-shadow:0 0 60px rgba(0,0,0,0.5);position:relative;';

            // Notch
            const notch = document.createElement('div');
            notch.style.cssText = 'width:120px;height:24px;background:#000;border-radius:0 0 16px 16px;position:absolute;top:0;left:50%;transform:translateX(-50%);z-index:2;';

            const screen = document.createElement('iframe');
            screen.style.cssText = 'width:100%;height:100%;border:none;border-radius:24px;background:#fff;';

            const closeBtn = document.createElement('button');
            closeBtn.textContent = 'Close Preview';
            closeBtn.style.cssText = 'position:absolute;bottom:-50px;left:50%;transform:translateX(-50%);padding:10px 24px;background:#9A7B4F;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;';
            closeBtn.addEventListener('click', () => overlay.remove());
            overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.remove(); });

            phone.appendChild(notch);
            phone.appendChild(screen);
            phone.appendChild(closeBtn);
            overlay.appendChild(phone);
            document.body.appendChild(overlay);

            // Write content into iframe
            screen.addEventListener('load', () => {
                const doc = screen.contentDocument;
                doc.open();
                doc.write(`<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><style>${css} body{margin:0;font-family:system-ui,-apple-system,sans-serif;}</style></head><body>${html}</body></html>`);
                doc.close();
            });
            // Trigger load
            screen.src = 'about:blank';
        },
    });

    // Expose for toolbar button
    window.openMobilePreview = function() {
        editor.runCommand('mobile-preview');
    };
}
