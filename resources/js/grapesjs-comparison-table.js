/**
 * Comparison Table block for GrapesJS builder.
 * Feature comparison with checkmarks/crosses, perfect for product vs product pages.
 */
export default function registerComparisonTable(editor) {
    editor.BlockManager.add('comparison-table', {
        label: 'Comparison Table',
        category: 'Marketing',
        media: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/></svg>`,
        content: comparisonHtml(),
    });
}

function comparisonHtml() {
    const check = '&#10003;';
    const cross = '&#10007;';
    const features = [
        ['Research-Grade Purity', check, check, cross],
        ['Third-Party Lab Tested', check, check, cross],
        ['Free Shipping', check, cross, cross],
        ['Expert Support', check, cross, cross],
        ['Money-Back Guarantee', check, cross, cross],
    ];

    const rows = features.map(([feat, a, b, c]) => `
        <tr>
            <td style="padding:14px 20px;border-bottom:1px solid #e8e4de;text-align:left;font-weight:500;color:#374151;">${feat}</td>
            <td style="padding:14px 20px;border-bottom:1px solid #e8e4de;text-align:center;">
                <span style="color:${a === check ? '#10b981' : '#ef4444'};font-size:20px;font-weight:700;">${a}</span>
            </td>
            <td style="padding:14px 20px;border-bottom:1px solid #e8e4de;text-align:center;">
                <span style="color:${b === check ? '#10b981' : '#ef4444'};font-size:20px;font-weight:700;">${b}</span>
            </td>
            <td style="padding:14px 20px;border-bottom:1px solid #e8e4de;text-align:center;">
                <span style="color:${c === check ? '#10b981' : '#ef4444'};font-size:20px;font-weight:700;">${c}</span>
            </td>
        </tr>`).join('');

    return `
    <section style="width:100%;padding:60px 20px;background:#f8f5f0;">
        <div style="max-width:900px;margin:0 auto;">
            <h2 style="text-align:center;font-size:32px;font-weight:700;color:#1a1714;margin-bottom:8px;">How We Compare</h2>
            <p style="text-align:center;color:#6b7280;margin-bottom:40px;">See why thousands choose us over the competition</p>
            <div style="overflow-x:auto;border-radius:12px;border:1px solid #e8e4de;background:#fff;">
                <table style="width:100%;border-collapse:collapse;min-width:500px;">
                    <thead>
                        <tr style="background:linear-gradient(135deg,#1a1714,#2a2520);">
                            <th style="padding:16px 20px;text-align:left;color:#d1d5db;font-weight:600;font-size:14px;text-transform:uppercase;letter-spacing:0.05em;">Feature</th>
                            <th style="padding:16px 20px;text-align:center;color:#C9A227;font-weight:700;font-size:15px;">Us</th>
                            <th style="padding:16px 20px;text-align:center;color:#d1d5db;font-weight:600;font-size:14px;">Competitor A</th>
                            <th style="padding:16px 20px;text-align:center;color:#d1d5db;font-weight:600;font-size:14px;">Competitor B</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
        </div>
    </section>`;
}
