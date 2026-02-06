/**
 * Sticky CTA bar block for GrapesJS builder.
 * Fixed bottom bar that appears after scrolling. Configurable via traits.
 */
export default function registerStickyCta(editor) {
    editor.DomComponents.addType('sticky-cta', {
        isComponent: (el) => el?.hasAttribute?.('data-sticky-cta'),
        model: {
            defaults: {
                traits: [
                    { type: 'number', name: 'data-sticky-scroll', label: 'Show after scroll %', placeholder: '30', min: 0, max: 100 },
                    { type: 'checkbox', name: 'data-sticky-closable', label: 'Allow close' },
                    { type: 'text', name: 'data-sticky-id', label: 'CTA ID (unique)', placeholder: 'sticky-1' },
                ],
            },
        },
    });

    editor.BlockManager.add('sticky-cta', {
        label: 'Sticky CTA Bar',
        category: 'Marketing',
        media: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="17" width="20" height="5" rx="1"/><path d="M12 3v10m-4-4l4 4 4-4"/></svg>`,
        content: stickyCtaHtml(),
    });
}

function stickyCtaHtml() {
    return `
    <div data-sticky-cta data-sticky-scroll="30" data-sticky-closable="true" data-sticky-id="sticky-1"
         style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:9980;background:linear-gradient(135deg,#1a1714,#2a2520);box-shadow:0 -4px 20px rgba(0,0,0,0.3);padding:16px 24px;">
        <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;align-items:center;gap:16px;flex:1;min-width:200px;">
                <div style="width:40px;height:40px;background:linear-gradient(135deg,#C9A227,#9A7B4F);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
                <div>
                    <p style="color:#f5f1eb;font-weight:700;font-size:16px;margin:0;">Limited Time Offer!</p>
                    <p style="color:#9ca3af;font-size:13px;margin:2px 0 0;">Get 20% off your first order — ends soon</p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <a href="#" style="display:inline-block;padding:12px 32px;background:linear-gradient(135deg,#C9A227,#9A7B4F);color:#fff;font-weight:700;border-radius:8px;text-decoration:none;font-size:15px;white-space:nowrap;">Shop Now →</a>
                <button data-sticky-close style="background:none;border:none;color:#6b7280;cursor:pointer;padding:4px;font-size:20px;line-height:1;">&times;</button>
            </div>
        </div>
    </div>`;
}
