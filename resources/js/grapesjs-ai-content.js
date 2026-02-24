/**
 * AI Content generation panel for GrapesJS.
 * Sends prompt to backend API proxy, inserts result into selected component.
 */
export default function registerAiContent(editor) {
    editor.Commands.add('ai-content-panel', {
        run(editor) {
            if (document.getElementById('pp-ai-panel')) {
                document.getElementById('pp-ai-panel').remove();
                return;
            }

            const panel = document.createElement('div');
            panel.id = 'pp-ai-panel';
            panel.style.cssText = 'position:fixed;top:56px;right:0;width:360px;height:calc(100vh - 56px);background:#1f2937;border-left:1px solid #374151;z-index:100;display:flex;flex-direction:column;';

            panel.innerHTML = `
                <div style="padding:16px;border-bottom:1px solid #374151;display:flex;justify-content:space-between;align-items:center;">
                    <h3 style="color:#fff;font-size:16px;font-weight:600;margin:0;">AI Content</h3>
                    <button id="pp-ai-close" style="background:none;border:none;color:#9ca3af;font-size:20px;cursor:pointer;">&times;</button>
                </div>
                <div style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label style="display:block;color:#9ca3af;font-size:13px;margin-bottom:6px;">Content Type</label>
                        <select id="pp-ai-type" style="width:100%;background:#374151;color:#e5e7eb;border:1px solid #4b5563;border-radius:8px;padding:8px 12px;font-size:14px;">
                            <option value="headline">Headline</option>
                            <option value="paragraph">Paragraph</option>
                            <option value="cta">CTA Text</option>
                            <option value="benefits">Benefits List</option>
                            <option value="testimonial">Testimonial</option>
                            <option value="faq">FAQ Answer</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;color:#9ca3af;font-size:13px;margin-bottom:6px;">Prompt / Context</label>
                        <textarea id="pp-ai-prompt" rows="4" placeholder="Describe what you want..." style="width:100%;background:#374151;color:#e5e7eb;border:1px solid #4b5563;border-radius:8px;padding:10px 12px;font-size:14px;resize:vertical;"></textarea>
                    </div>
                    <div>
                        <label style="display:block;color:#9ca3af;font-size:13px;margin-bottom:6px;">Tone</label>
                        <select id="pp-ai-tone" style="width:100%;background:#374151;color:#e5e7eb;border:1px solid #4b5563;border-radius:8px;padding:8px 12px;font-size:14px;">
                            <option value="professional">Professional</option>
                            <option value="casual">Casual</option>
                            <option value="persuasive">Persuasive</option>
                            <option value="urgent">Urgent</option>
                            <option value="friendly">Friendly</option>
                        </select>
                    </div>
                    <button id="pp-ai-generate" style="padding:12px;background:#9A7B4F;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:14px;">Generate Content</button>
                    <div id="pp-ai-result" style="display:none;">
                        <label style="display:block;color:#9ca3af;font-size:13px;margin-bottom:6px;">Result</label>
                        <div id="pp-ai-output" style="background:#374151;color:#e5e7eb;border:1px solid #4b5563;border-radius:8px;padding:12px;font-size:14px;line-height:1.6;max-height:200px;overflow-y:auto;white-space:pre-wrap;"></div>
                        <div style="display:flex;gap:8px;margin-top:10px;">
                            <button id="pp-ai-insert" style="flex:1;padding:10px;background:#10b981;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:13px;">Insert into Selected</button>
                            <button id="pp-ai-copy" style="padding:10px 16px;background:#374151;color:#e5e7eb;border:1px solid #4b5563;border-radius:8px;cursor:pointer;font-size:13px;">Copy</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(panel);

            panel.querySelector('#pp-ai-close').addEventListener('click', () => panel.remove());

            panel.querySelector('#pp-ai-generate').addEventListener('click', async () => {
                const btn = panel.querySelector('#pp-ai-generate');
                const type = panel.querySelector('#pp-ai-type').value;
                const prompt = panel.querySelector('#pp-ai-prompt').value.trim();
                const tone = panel.querySelector('#pp-ai-tone').value;

                if (!prompt) { window.showEditorToast?.('Enter a prompt first', 'error'); return; }

                btn.textContent = 'Generating...';
                btn.disabled = true;

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                    const r = await fetch('/admin/ai-content/generate', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({ type, prompt, tone }),
                    });
                    if (!r.ok) throw new Error(r.status);
                    const data = await r.json();
                    if (data.content) {
                        panel.querySelector('#pp-ai-output').textContent = data.content;
                        panel.querySelector('#pp-ai-result').style.display = 'block';
                    } else {
                        window.showEditorToast?.(data.error || 'Generation failed', 'error');
                    }
                } catch {
                    window.showEditorToast?.('AI generation failed. Check API key in Settings.', 'error');
                }
                btn.textContent = 'Generate Content';
                btn.disabled = false;
            });

            // Insert into selected component
            panel.querySelector('#pp-ai-insert').addEventListener('click', () => {
                const selected = editor.getSelected();
                const text = panel.querySelector('#pp-ai-output').textContent;
                if (!selected) { window.showEditorToast?.('Select an element first', 'error'); return; }
                // Set inner HTML for text-based components
                selected.components(text);
                window.showEditorToast?.('Content inserted', 'success');
            });

            // Copy to clipboard
            panel.querySelector('#pp-ai-copy').addEventListener('click', () => {
                const text = panel.querySelector('#pp-ai-output').textContent;
                navigator.clipboard.writeText(text).then(() => {
                    window.showEditorToast?.('Copied to clipboard', 'success');
                });
            });
        },
    });

    window.openAiContent = function() { editor.runCommand('ai-content-panel'); };
}
