/**
 * Video Testimonial block for GrapesJS builder.
 * Embedded video alongside a quote, name, and role.
 */
export default function registerVideoTestimonial(editor) {
    editor.BlockManager.add('video-testimonial', {
        label: 'Video Testimonial',
        category: 'Marketing',
        media: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><polygon points="10 8 16 12 10 16 10 8" fill="currentColor" stroke="none"/></svg>`,
        content: videoTestimonialHtml(),
    });
}

function videoTestimonialHtml() {
    return `
    <section style="width:100%;padding:80px 20px;background:linear-gradient(135deg,#1a1714,#2a2520);">
        <div style="max-width:1100px;margin:0 auto;display:flex;flex-wrap:wrap;gap:48px;align-items:center;">
            <!-- Video -->
            <div style="flex:1;min-width:300px;">
                <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:12px;box-shadow:0 20px 40px rgba(0,0,0,0.3);">
                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
            <!-- Quote -->
            <div style="flex:1;min-width:280px;">
                <div style="margin-bottom:24px;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="#C9A227"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                </div>
                <blockquote style="font-size:20px;line-height:1.6;color:#f5f1eb;font-style:italic;margin-bottom:32px;">
                    "This product completely changed my approach to research. The quality is unmatched, and the results speak for themselves. I've recommended it to every colleague."
                </blockquote>
                <div style="display:flex;align-items:center;gap:16px;">
                    <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#C9A227,#9A7B4F);display:flex;align-items:center;justify-content:center;">
                        <span style="color:#fff;font-weight:700;font-size:20px;">DR</span>
                    </div>
                    <div>
                        <p style="color:#f5f1eb;font-weight:700;font-size:16px;margin:0;">Dr. Research Expert</p>
                        <p style="color:#9A7B4F;font-size:14px;margin:4px 0 0;">Lead Researcher, Institute of Science</p>
                    </div>
                </div>
                <div style="display:flex;gap:4px;margin-top:16px;">
                    <span style="color:#C9A227;font-size:20px;">&#9733;</span>
                    <span style="color:#C9A227;font-size:20px;">&#9733;</span>
                    <span style="color:#C9A227;font-size:20px;">&#9733;</span>
                    <span style="color:#C9A227;font-size:20px;">&#9733;</span>
                    <span style="color:#C9A227;font-size:20px;">&#9733;</span>
                </div>
            </div>
        </div>
    </section>`;
}
