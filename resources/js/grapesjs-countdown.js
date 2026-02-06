/**
 * Countdown Timer — GrapesJS block & component type
 * Supports fixed-date and evergreen (per-visitor) modes.
 * Runtime logic lives in page-components.js (initCountdownTimers).
 */
export default function registerCountdownTimer(editor) {
    // Custom component type — gives the user configurable traits in the Settings panel
    editor.DomComponents.addType('countdown-timer', {
        isComponent: (el) => el?.hasAttribute?.('data-countdown'),
        model: {
            defaults: {
                traits: [
                    {
                        type: 'select',
                        name: 'data-countdown-mode',
                        label: 'Mode',
                        options: [
                            { value: 'fixed', name: 'Fixed Date' },
                            { value: 'evergreen', name: 'Evergreen (per visitor)' },
                        ],
                    },
                    {
                        type: 'text',
                        name: 'data-countdown-date',
                        label: 'Target Date',
                        placeholder: '2026-12-31T23:59',
                    },
                    {
                        type: 'number',
                        name: 'data-countdown-evergreen',
                        label: 'Evergreen Minutes',
                        placeholder: '60',
                        min: 1,
                    },
                    {
                        type: 'text',
                        name: 'data-countdown-expired',
                        label: 'Expired Message',
                        placeholder: 'Offer has expired!',
                    },
                    {
                        type: 'text',
                        name: 'data-countdown-id',
                        label: 'Timer ID (unique per page)',
                        placeholder: 'timer-1',
                    },
                ],
            },
        },
    });

    // Block definition
    editor.BlockManager.add('countdown-timer', {
        label: 'Countdown Timer',
        category: 'Marketing',
        content: countdownHtml(),
        media: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>`,
    });
}

function countdownHtml() {
    const box = 'background:rgba(255,255,255,0.08);border-radius:12px;padding:20px 24px;min-width:90px;';
    const digit = 'font-size:40px;font-weight:700;color:#fff;line-height:1;';
    const label = 'font-size:12px;color:#9A7B4F;text-transform:uppercase;letter-spacing:1px;margin-top:8px;';
    const sep = 'display:flex;align-items:center;color:#9A7B4F;font-size:32px;font-weight:300;';

    return `
<div data-countdown data-countdown-mode="fixed" data-countdown-date="2026-12-31T23:59" data-countdown-expired="Offer has expired!" data-countdown-id="timer-1" style="background:linear-gradient(135deg,#1a1714 0%,#2a2520 100%);padding:50px 20px;text-align:center;">
    <h3 style="color:#C9A227;font-size:14px;text-transform:uppercase;letter-spacing:3px;margin:0 0 10px;">Limited Time Offer</h3>
    <h2 style="color:#fff;font-size:28px;font-weight:700;margin:0 0 30px;">Don't Miss Out — Sale Ends Soon!</h2>
    <div style="display:flex;justify-content:center;gap:16px;flex-wrap:wrap;">
        <div style="${box}"><div data-cd-days style="${digit}">00</div><div style="${label}">Days</div></div>
        <div style="${sep}">:</div>
        <div style="${box}"><div data-cd-hours style="${digit}">00</div><div style="${label}">Hours</div></div>
        <div style="${sep}">:</div>
        <div style="${box}"><div data-cd-mins style="${digit}">00</div><div style="${label}">Minutes</div></div>
        <div style="${sep}">:</div>
        <div style="${box}"><div data-cd-secs style="${digit}">00</div><div style="${label}">Seconds</div></div>
    </div>
    <div data-cd-expired style="display:none;color:#C9A227;font-size:20px;font-weight:600;padding:20px 0;"></div>
</div>`;
}
