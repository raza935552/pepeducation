/**
 * Trust Badges block for GrapesJS builder.
 * Row of trust/credibility badges (guarantee, secure, shipping, etc.)
 */
export default function registerTrustBadges(editor) {
    editor.BlockManager.add('trust-badges', {
        label: 'Trust Badges',
        category: 'Marketing',
        media: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>`,
        content: trustBadgesHtml(),
    });
}

function trustBadgesHtml() {
    const badges = [
        { icon: shieldSvg(), title: '30-Day Guarantee', sub: 'Full refund, no questions' },
        { icon: lockSvg(), title: 'Secure Checkout', sub: '256-bit SSL encryption' },
        { icon: labSvg(), title: 'Lab Tested', sub: '3rd-party verified purity' },
        { icon: truckSvg(), title: 'Free Shipping', sub: 'On orders over $99' },
    ];

    const cards = badges.map(b => `
        <div style="flex:1;min-width:180px;text-align:center;padding:24px 16px;">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,#C9A227,#9A7B4F);border-radius:50%;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
                ${b.icon}
            </div>
            <h4 style="font-size:15px;font-weight:700;color:#1a1714;margin-bottom:4px;">${b.title}</h4>
            <p style="font-size:13px;color:#6b7280;margin:0;">${b.sub}</p>
        </div>`).join('');

    return `
    <section style="width:100%;padding:40px 20px;background:#fff;border-top:1px solid #e8e4de;border-bottom:1px solid #e8e4de;">
        <div style="max-width:960px;margin:0 auto;display:flex;flex-wrap:wrap;justify-content:center;gap:8px;">
            ${cards}
        </div>
    </section>`;
}

function shieldSvg() {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>';
}
function lockSvg() {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>';
}
function labSvg() {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M9 3h6m-5 0v6.5L4 18a1 1 0 001 1h14a1 1 0 001-1l-6-8.5V3"/></svg>';
}
function truckSvg() {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>';
}
