/**
 * Form Builder block for GrapesJS.
 * Pre-styled contact/lead form with AJAX submission.
 * Configurable form name and success message via traits.
 */
export default function registerFormBuilder(editor) {
    editor.DomComponents.addType('builder-form', {
        isComponent: (el) => el?.hasAttribute?.('data-builder-form'),
        model: {
            defaults: {
                traits: [
                    { type: 'text', name: 'data-form-name', label: 'Form Name', placeholder: 'contact' },
                    { type: 'text', name: 'data-form-success', label: 'Success Message', placeholder: 'Thanks! We\'ll be in touch.' },
                ],
            },
        },
    });

    editor.BlockManager.add('builder-form', {
        label: 'Lead Form',
        category: 'Marketing',
        media: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="17" y2="12"/><rect x="7" y="16" width="10" height="2" rx="1" fill="currentColor" stroke="none"/></svg>`,
        content: formHtml(),
    });
}

function formHtml() {
    return `
    <section style="width:100%;padding:60px 20px;background:#f8f5f0;">
        <div data-builder-form data-form-name="contact" data-form-success="Thanks! We'll be in touch shortly."
             style="max-width:560px;margin:0 auto;background:#fff;border-radius:16px;padding:40px;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
            <h3 style="font-size:24px;font-weight:700;color:#1a1714;text-align:center;margin-bottom:8px;">Get in Touch</h3>
            <p style="text-align:center;color:#6b7280;margin-bottom:32px;">Fill out the form below and we'll respond within 24 hours.</p>
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:200px;">
                        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">First Name</label>
                        <input type="text" name="first_name" placeholder="John" required
                               style="width:100%;padding:12px 16px;border:1px solid #e8e4de;border-radius:8px;font-size:15px;outline:none;box-sizing:border-box;">
                    </div>
                    <div style="flex:1;min-width:200px;">
                        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Last Name</label>
                        <input type="text" name="last_name" placeholder="Doe"
                               style="width:100%;padding:12px 16px;border:1px solid #e8e4de;border-radius:8px;font-size:15px;outline:none;box-sizing:border-box;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" required
                           style="width:100%;padding:12px 16px;border:1px solid #e8e4de;border-radius:8px;font-size:15px;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">Message</label>
                    <textarea name="message" rows="4" placeholder="How can we help you?"
                              style="width:100%;padding:12px 16px;border:1px solid #e8e4de;border-radius:8px;font-size:15px;outline:none;resize:vertical;box-sizing:border-box;"></textarea>
                </div>
                <button type="button" data-form-submit
                        style="width:100%;padding:14px;background:linear-gradient(135deg,#C9A227,#9A7B4F);color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:700;cursor:pointer;">
                    Send Message
                </button>
            </div>
            <div data-form-status style="display:none;text-align:center;padding:16px;margin-top:16px;border-radius:8px;font-weight:600;"></div>
        </div>
    </section>`;
}
