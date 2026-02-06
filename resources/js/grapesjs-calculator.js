/**
 * Calculator Widget block for GrapesJS.
 * Configurable formula-based calculator with traits.
 */
export default function registerCalculator(editor) {
    const formulas = [
        { value: 'sum', name: 'Sum' },
        { value: 'bmi', name: 'BMI (weight kg, height cm)' },
        { value: 'dosage', name: 'Dosage (weight × factor)' },
    ];

    editor.DomComponents.addType('calculator-widget', {
        isComponent: (el) => el?.hasAttribute?.('data-calculator'),
        model: {
            defaults: {
                traits: [
                    { type: 'select', name: 'data-calc-formula', label: 'Formula', options: formulas },
                    { type: 'text', name: 'data-calc-title', label: 'Title', placeholder: 'Quick Calculator' },
                ],
            },
        },
    });

    editor.BlockManager.add('calculator-widget', {
        label: 'Calculator',
        category: 'Interactive',
        content: `
            <div data-calculator data-calc-formula="sum" style="max-width:500px;margin:30px auto;background:#f8f5f0;border-radius:16px;padding:30px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <h3 style="text-align:center;color:#1a1714;margin:0 0 20px;">Quick Calculator</h3>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <label style="min-width:80px;color:#333;font-size:14px;">Value 1</label>
                        <input data-calc-input type="number" placeholder="0" style="flex:1;padding:10px 14px;border:1px solid #e8e4de;border-radius:8px;font-size:16px;background:#fff;" />
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <label style="min-width:80px;color:#333;font-size:14px;">Value 2</label>
                        <input data-calc-input type="number" placeholder="0" style="flex:1;padding:10px 14px;border:1px solid #e8e4de;border-radius:8px;font-size:16px;background:#fff;" />
                    </div>
                    <button data-calc-btn style="margin-top:8px;padding:14px;background:#9A7B4F;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;">Calculate</button>
                    <div data-calc-result style="display:none;text-align:center;padding:16px;background:#1a1714;color:#C9A227;border-radius:8px;font-size:24px;font-weight:700;margin-top:8px;">0</div>
                </div>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><circle cx="8" cy="11" r="1" fill="currentColor"/><circle cx="12" cy="11" r="1" fill="currentColor"/><circle cx="16" cy="11" r="1" fill="currentColor"/><circle cx="8" cy="15" r="1" fill="currentColor"/><circle cx="12" cy="15" r="1" fill="currentColor"/><circle cx="16" cy="15" r="1" fill="currentColor"/></svg>`,
    });
}
