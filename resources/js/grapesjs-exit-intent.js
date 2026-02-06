/**
 * Exit Intent Popup block for GrapesJS builder.
 * Shows an overlay when user moves mouse toward browser chrome (desktop).
 * Configurable via traits: delay, show-once, popup ID.
 */
export default function registerExitIntent(editor) {
    editor.DomComponents.addType('exit-intent-popup', {
        isComponent: (el) => el?.hasAttribute?.('data-exit-intent'),
        model: {
            defaults: {
                traits: [
                    { type: 'number', name: 'data-exit-delay', label: 'Delay (ms)', placeholder: '0', min: 0 },
                    { type: 'checkbox', name: 'data-exit-once', label: 'Show Once Only' },
                    { type: 'text', name: 'data-exit-id', label: 'Popup ID (unique)', placeholder: 'exit-1' },
                ],
            },
        },
    });

    editor.BlockManager.add('exit-intent-popup', {
        label: 'Exit Intent Popup',
        category: 'Marketing',
        media: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>`,
        content: exitIntentHtml(),
    });
}

function exitIntentHtml() {
    return `
    <div data-exit-intent data-exit-once="true" data-exit-id="exit-1" data-exit-delay="0"
         style="display:none;position:fixed;inset:0;z-index:9990;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:16px;max-width:500px;width:90%;padding:48px 40px;text-align:center;position:relative;box-shadow:0 25px 50px rgba(0,0,0,0.25);">
            <button data-exit-close style="position:absolute;top:12px;right:12px;background:none;border:none;font-size:24px;color:#9ca3af;cursor:pointer;padding:4px 8px;line-height:1;">&times;</button>
            <div style="width:64px;height:64px;background:linear-gradient(135deg,#C9A227,#9A7B4F);border-radius:50%;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;">
                <span style="color:#fff;font-size:28px;">!</span>
            </div>
            <h3 style="font-size:26px;font-weight:700;color:#1a1714;margin-bottom:12px;">Wait! Don't Leave Yet</h3>
            <p style="color:#6b7280;font-size:16px;line-height:1.6;margin-bottom:24px;">Get 10% off your first order. Enter your email below and we'll send you an exclusive discount code.</p>
            <div style="display:flex;gap:8px;max-width:380px;margin:0 auto;">
                <input type="email" placeholder="Enter your email" style="flex:1;padding:12px 16px;border:1px solid #e8e4de;border-radius:8px;font-size:15px;outline:none;">
                <button style="padding:12px 24px;background:linear-gradient(135deg,#C9A227,#9A7B4F);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;white-space:nowrap;">Get 10% Off</button>
            </div>
            <p style="margin-top:12px;font-size:12px;color:#9ca3af;">No spam. Unsubscribe anytime.</p>
        </div>
    </div>`;
}
