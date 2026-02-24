import grapesjs from 'grapesjs';
import gjsBlocksBasic from 'grapesjs-blocks-basic';
import gjsPluginForms from 'grapesjs-plugin-forms';
import gjsNavbar from 'grapesjs-navbar';
import gjsTabs from 'grapesjs-tabs';
import gjsCustomCode from 'grapesjs-custom-code';
import gjsTouch from 'grapesjs-touch';
import html2canvas from 'html2canvas';

import registerCountdownTimer from './grapesjs-countdown';
import registerUnsplash from './grapesjs-unsplash';
import registerMediaLibrary from './grapesjs-media-library';
import registerGlobalColors from './grapesjs-global-colors';
import registerComparisonTable from './grapesjs-comparison-table';
import registerExitIntent from './grapesjs-exit-intent';
import registerTrustBadges from './grapesjs-trust-badges';
import registerVideoTestimonial from './grapesjs-video-testimonial';
import registerStickyCta from './grapesjs-sticky-cta';
import registerFormBuilder from './grapesjs-form-builder';
import registerSavedSections from './grapesjs-saved-sections';
import registerVersionHistory from './grapesjs-version-history';
import registerPageAnalytics from './grapesjs-page-analytics';
import registerAnimations from './grapesjs-animations';
import registerCalculator from './grapesjs-calculator';
import registerMobilePreview from './grapesjs-mobile-preview';
import registerImportExport from './grapesjs-import-export';
import registerAiContent from './grapesjs-ai-content';
import 'grapesjs/dist/css/grapes.min.css';

window.initGrapesJS = function(config = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const editor = grapesjs.init({
        container: '#gjs',
        height: '100%',
        width: 'auto',
        fromElement: false,
        storageManager: false,

        plugins: [
            gjsBlocksBasic,
            gjsPluginForms,
            gjsNavbar,
            gjsTabs,
            gjsCustomCode,
            gjsTouch,
        ],

        pluginsOpts: {
            [gjsBlocksBasic]: {
                flexGrid: true,
            },
            [gjsPluginForms]: {},
            [gjsNavbar]: {},
            [gjsTabs]: {},
            [gjsCustomCode]: {},
            [gjsTouch]: {},
        },

        assetManager: {
            upload: '/admin/pages/upload-image',
            uploadName: 'image',
            multiUpload: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            autoAdd: true,
            assets: config.assets || [],
        },

        canvas: {
            styles: config.canvasStyles || [],
        },

        deviceManager: {
            devices: [
                { name: 'Desktop', width: '' },
                { name: 'Tablet', width: '768px', widthMedia: '768px' },
                { name: 'Mobile', width: '375px', widthMedia: '375px' },
            ]
        },

        panels: {
            defaults: []
        },

        blockManager: {
            appendTo: '#blocks-panel',
        },

        layerManager: {
            appendTo: '#layers-panel',
        },

        styleManager: {
            appendTo: '#styles-panel',
            sectors: [
                {
                    name: 'Dimension',
                    open: false,
                    buildProps: ['width', 'min-width', 'max-width', 'height', 'min-height', 'max-height', 'margin', 'padding'],
                },
                {
                    name: 'Typography',
                    open: false,
                    buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
                },
                {
                    name: 'Background',
                    open: false,
                    buildProps: ['background-color', 'background-image', 'background-repeat', 'background-position', 'background-size'],
                },
                {
                    name: 'Border',
                    open: false,
                    buildProps: ['border-radius', 'border', 'box-shadow'],
                },
                {
                    name: 'Extra',
                    open: false,
                    buildProps: ['opacity', 'transition', 'transform'],
                },
                {
                    name: 'Flex',
                    open: false,
                    buildProps: ['flex-direction', 'flex-wrap', 'justify-content', 'align-items', 'align-content', 'order', 'flex-basis', 'flex-grow', 'flex-shrink', 'align-self'],
                },
            ],
        },

        traitManager: {
            appendTo: '#traits-panel',
        },

        selectorManager: {
            appendTo: '#selectors-panel',
        },

        undoManager: {
            maximumStackLength: 50,
        },
    });

    // Load existing content if provided (defer to ensure Canvas is ready)
    if (config.projectData) {
        editor.onReady(() => {
            try {
                editor.loadProjectData(config.projectData);
            } catch (e) {
                // GrapeJS 0.22.x race condition — Canvas.getFrames may not be ready
                console.warn('GrapesJS loadProjectData deferred retry:', e.message);
                setTimeout(() => editor.loadProjectData(config.projectData), 100);
            }
        });
    }

    // Handle image upload errors
    editor.on('asset:upload:error', (error) => {
        const msg = error?.message || 'Image upload failed. Please check file size (max 5MB) and format (JPG, PNG, WebP, GIF).';
        showEditorToast(msg, 'error');
    });

    editor.on('asset:upload:response', (response) => {
        if (response && !response.data) {
            showEditorToast('Upload failed: invalid server response.', 'error');
        }
    });

    // Custom commands
    addCustomCommands(editor);

    // Add custom blocks
    addCustomBlocks(editor);

    // Register countdown timer block & component type
    registerCountdownTimer(editor);

    // Register Unsplash stock photo integration
    registerUnsplash(editor);

    // Register Media Library
    registerMediaLibrary(editor);
    registerGlobalColors(editor);
    registerComparisonTable(editor);
    registerExitIntent(editor);
    registerTrustBadges(editor);
    registerVideoTestimonial(editor);
    registerStickyCta(editor);
    registerFormBuilder(editor);
    registerSavedSections(editor);
    registerVersionHistory(editor);
    registerPageAnalytics(editor);
    registerAnimations(editor);
    registerCalculator(editor);
    registerMobilePreview(editor);
    registerImportExport(editor);
    registerAiContent(editor);

    // Make editor globally accessible
    window.gjsEditor = editor;

    return editor;
};

function addCustomCommands(editor) {
    const commands = editor.Commands;

    // Device switching commands
    commands.add('set-device-desktop', {
        run: (editor) => editor.setDevice('Desktop'),
    });
    commands.add('set-device-tablet', {
        run: (editor) => editor.setDevice('Tablet'),
    });
    commands.add('set-device-mobile', {
        run: (editor) => editor.setDevice('Mobile'),
    });
}

function addCustomBlocks(editor) {
    const bm = editor.BlockManager;

    // ===== LAYOUT BLOCKS =====

    // Container Block
    bm.add('container', {
        label: 'Container',
        category: 'Layout',
        content: `
            <section style="width:100%; padding:60px 20px; background-color:#f8f5f0;">
                <div style="max-width:1200px; margin:0 auto;">
                    <p>Container content here</p>
                </div>
            </section>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="4" width="20" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><rect x="5" y="7" width="14" height="10" rx="1" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>`,
    });

    // ===== CAROUSEL BLOCKS =====

    // Image Carousel
    bm.add('carousel-image', {
        label: 'Image Carousel',
        category: 'Carousel',
        content: `
            <div data-carousel data-autoplay="5000" style="position:relative; width:100%; overflow:hidden; border-radius:12px;">
                <div data-carousel-track style="display:flex; transition:transform 0.5s ease;">
                    <div data-carousel-slide style="min-width:100%; position:relative;">
                        <img src="https://placehold.co/1200x500/9A7B4F/ffffff?text=Slide+1" style="width:100%; height:auto; display:block;" alt="Slide 1" />
                        <div style="position:absolute; bottom:20px; left:20px; color:white; text-shadow:0 2px 4px rgba(0,0,0,0.5);">
                            <h3 style="margin:0; font-size:24px;">Slide Title</h3>
                            <p style="margin:5px 0 0 0;">Slide description text</p>
                        </div>
                    </div>
                    <div data-carousel-slide style="min-width:100%; position:relative;">
                        <img src="https://placehold.co/1200x500/A67B5B/ffffff?text=Slide+2" style="width:100%; height:auto; display:block;" alt="Slide 2" />
                        <div style="position:absolute; bottom:20px; left:20px; color:white; text-shadow:0 2px 4px rgba(0,0,0,0.5);">
                            <h3 style="margin:0; font-size:24px;">Slide Title</h3>
                            <p style="margin:5px 0 0 0;">Slide description text</p>
                        </div>
                    </div>
                    <div data-carousel-slide style="min-width:100%; position:relative;">
                        <img src="https://placehold.co/1200x500/C9A227/ffffff?text=Slide+3" style="width:100%; height:auto; display:block;" alt="Slide 3" />
                        <div style="position:absolute; bottom:20px; left:20px; color:white; text-shadow:0 2px 4px rgba(0,0,0,0.5);">
                            <h3 style="margin:0; font-size:24px;">Slide Title</h3>
                            <p style="margin:5px 0 0 0;">Slide description text</p>
                        </div>
                    </div>
                </div>
                <button data-carousel-prev style="position:absolute;top:50%;left:10px;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:#fff;border:none;width:40px;height:40px;border-radius:50%;cursor:pointer;font-size:18px;" aria-label="Previous slide">&#8249;</button>
                <button data-carousel-next style="position:absolute;top:50%;right:10px;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:#fff;border:none;width:40px;height:40px;border-radius:50%;cursor:pointer;font-size:18px;" aria-label="Next slide">&#8250;</button>
                <div data-carousel-dots style="position:absolute; bottom:15px; left:50%; transform:translateX(-50%); display:flex; gap:8px;"></div>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="6" width="20" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="16" r="1"/><circle cx="9" cy="16" r="1"/><circle cx="15" cy="16" r="1"/></svg>`,
    });

    // Testimonial Carousel
    bm.add('carousel-testimonial', {
        label: 'Testimonial Carousel',
        category: 'Carousel',
        content: `
            <div style="background:#f8f5f0; padding:60px 20px; text-align:center;">
                <div style="max-width:800px; margin:0 auto;">
                    <div style="font-size:48px; color:#9A7B4F; line-height:1;">"</div>
                    <p style="font-size:20px; line-height:1.8; color:#333; font-style:italic; margin:20px 0;">
                        This product completely transformed my results. I've never experienced anything like it. The quality is outstanding and the support team is incredibly helpful.
                    </p>
                    <div style="display:flex; align-items:center; justify-content:center; gap:15px; margin-top:30px;">
                        <img src="https://placehold.co/60x60/9A7B4F/ffffff?text=JD" style="width:60px; height:60px; border-radius:50%;" />
                        <div style="text-align:left;">
                            <p style="margin:0; font-weight:600; color:#333;">John Doe</p>
                            <p style="margin:0; color:#666; font-size:14px;">Verified Buyer</p>
                            <div style="color:#C9A227; font-size:14px;">★★★★★</div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 17c2.269-9.881 11-11.667 11-11.667v2.667s-5.731.667-7 6.667c-.387 1.828.667 4.333.667 4.333H6v-2z"/><path d="M13 17c2.269-9.881 11-11.667 11-11.667v2.667s-5.731.667-7 6.667c-.387 1.828.667 4.333.667 4.333H13v-2z" transform="translate(-6)"/></svg>`,
    });

    // Product Carousel
    bm.add('carousel-product', {
        label: 'Product Carousel',
        category: 'Carousel',
        content: `
            <div data-carousel style="position:relative; padding:40px 20px; background:#fff; overflow:hidden;">
                <h2 style="text-align:center; margin-bottom:30px; color:#333;">Featured Products</h2>
                <div data-carousel-track style="display:flex; transition:transform 0.5s ease;">
                    <div data-carousel-slide style="min-width:100%; display:flex; justify-content:center;">
                        <div style="max-width:280px; background:#f8f5f0; border-radius:12px; padding:20px; text-align:center;">
                            <img src="https://placehold.co/200x200/9A7B4F/ffffff?text=Product" style="width:100%; border-radius:8px;" alt="Product 1" />
                            <h3 style="margin:15px 0 10px; color:#333;">Product Name</h3>
                            <p style="color:#9A7B4F; font-weight:600; font-size:20px;">$99.00</p>
                            <button style="background:#9A7B4F; color:white; border:none; padding:12px 30px; border-radius:25px; cursor:pointer; font-weight:600;">Add to Cart</button>
                        </div>
                    </div>
                    <div data-carousel-slide style="min-width:100%; display:flex; justify-content:center;">
                        <div style="max-width:280px; background:#f8f5f0; border-radius:12px; padding:20px; text-align:center;">
                            <img src="https://placehold.co/200x200/A67B5B/ffffff?text=Product" style="width:100%; border-radius:8px;" alt="Product 2" />
                            <h3 style="margin:15px 0 10px; color:#333;">Product Name</h3>
                            <p style="color:#9A7B4F; font-weight:600; font-size:20px;">$149.00</p>
                            <button style="background:#9A7B4F; color:white; border:none; padding:12px 30px; border-radius:25px; cursor:pointer; font-weight:600;">Add to Cart</button>
                        </div>
                    </div>
                    <div data-carousel-slide style="min-width:100%; display:flex; justify-content:center;">
                        <div style="max-width:280px; background:#f8f5f0; border-radius:12px; padding:20px; text-align:center;">
                            <img src="https://placehold.co/200x200/C9A227/ffffff?text=Product" style="width:100%; border-radius:8px;" alt="Product 3" />
                            <h3 style="margin:15px 0 10px; color:#333;">Product Name</h3>
                            <p style="color:#9A7B4F; font-weight:600; font-size:20px;">$199.00</p>
                            <button style="background:#9A7B4F; color:white; border:none; padding:12px 30px; border-radius:25px; cursor:pointer; font-weight:600;">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <button data-carousel-prev style="position:absolute;top:50%;left:10px;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:#fff;border:none;width:40px;height:40px;border-radius:50%;cursor:pointer;font-size:18px;" aria-label="Previous product">&#8249;</button>
                <button data-carousel-next style="position:absolute;top:50%;right:10px;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:#fff;border:none;width:40px;height:40px;border-radius:50%;cursor:pointer;font-size:18px;" aria-label="Next product">&#8250;</button>
                <div data-carousel-dots style="text-align:center; margin-top:20px; display:flex; justify-content:center; gap:8px;"></div>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="5" width="6" height="14" rx="1" fill="none" stroke="currentColor" stroke-width="2"/><rect x="11" y="5" width="6" height="14" rx="1" fill="none" stroke="currentColor" stroke-width="2"/><path d="M19 9l2 3-2 3" fill="none" stroke="currentColor" stroke-width="2"/></svg>`,
    });

    // ===== MARKETING BLOCKS =====

    // Hero Section
    bm.add('hero-section', {
        label: 'Hero Section',
        category: 'Marketing',
        content: `
            <section style="background:linear-gradient(135deg, #f8f5f0 0%, #e8e4de 100%); padding:80px 20px;">
                <div style="max-width:1200px; margin:0 auto; display:flex; align-items:center; gap:60px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:300px;">
                        <span style="background:#9A7B4F; color:white; padding:6px 16px; border-radius:20px; font-size:14px; font-weight:500;">New Release</span>
                        <h1 style="font-size:48px; line-height:1.2; margin:20px 0; color:#1a1714;">Transform Your Results Today</h1>
                        <p style="font-size:18px; color:#666; line-height:1.7; margin-bottom:30px;">Experience the breakthrough formula that thousands trust. Backed by science, loved by customers.</p>
                        <div style="display:flex; gap:15px; flex-wrap:wrap;">
                            <button style="background:#1a1714; color:white; border:none; padding:16px 32px; border-radius:30px; font-size:16px; font-weight:600; cursor:pointer;">Get Started →</button>
                            <button style="background:transparent; color:#1a1714; border:2px solid #1a1714; padding:14px 32px; border-radius:30px; font-size:16px; font-weight:600; cursor:pointer;">Learn More</button>
                        </div>
                        <div style="display:flex; gap:30px; margin-top:40px;">
                            <div><span style="font-size:24px; font-weight:700; color:#9A7B4F;">10k+</span><br/><span style="color:#666; font-size:14px;">Happy Customers</span></div>
                            <div><span style="font-size:24px; font-weight:700; color:#9A7B4F;">4.9★</span><br/><span style="color:#666; font-size:14px;">Average Rating</span></div>
                        </div>
                    </div>
                    <div style="flex:1; min-width:300px;">
                        <img src="https://placehold.co/500x500/9A7B4F/ffffff?text=Product+Image" style="width:100%; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,0.15);" />
                    </div>
                </div>
            </section>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="3" width="20" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><line x1="5" y1="8" x2="12" y2="8" stroke="currentColor" stroke-width="2"/><line x1="5" y1="12" x2="10" y2="12" stroke="currentColor" stroke-width="2"/><rect x="14" y="7" width="5" height="8" rx="1" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>`,
    });

    // CTA Button
    bm.add('cta-button', {
        label: 'CTA Button',
        category: 'Marketing',
        content: `
            <div style="text-align:center; padding:20px;">
                <a href="#" style="display:inline-block; background:linear-gradient(135deg, #9A7B4F 0%, #C9A227 100%); color:white; text-decoration:none; padding:18px 50px; border-radius:50px; font-size:18px; font-weight:700; box-shadow:0 4px 15px rgba(154,123,79,0.4); transition:transform 0.2s;">
                    Get Started Now →
                </a>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="4" y="8" width="16" height="8" rx="4" fill="none" stroke="currentColor" stroke-width="2"/></svg>`,
    });

    // Benefits List
    bm.add('benefits-list', {
        label: 'Benefits List',
        category: 'Marketing',
        content: `
            <div style="padding:40px 20px; background:#fff;">
                <div style="max-width:600px; margin:0 auto;">
                    <h2 style="text-align:center; margin-bottom:30px; color:#333;">Why Choose Us</h2>
                    <div style="display:flex; flex-direction:column; gap:20px;">
                        <div style="display:flex; align-items:flex-start; gap:15px;">
                            <div style="width:28px; height:28px; background:#10b981; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <span style="color:white; font-size:16px;">✓</span>
                            </div>
                            <div>
                                <h4 style="margin:0 0 5px; color:#333;">Premium Quality Ingredients</h4>
                                <p style="margin:0; color:#666; font-size:14px;">Sourced from the highest quality suppliers worldwide.</p>
                            </div>
                        </div>
                        <div style="display:flex; align-items:flex-start; gap:15px;">
                            <div style="width:28px; height:28px; background:#10b981; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <span style="color:white; font-size:16px;">✓</span>
                            </div>
                            <div>
                                <h4 style="margin:0 0 5px; color:#333;">Third-Party Lab Tested</h4>
                                <p style="margin:0; color:#666; font-size:14px;">Every batch is independently verified for purity.</p>
                            </div>
                        </div>
                        <div style="display:flex; align-items:flex-start; gap:15px;">
                            <div style="width:28px; height:28px; background:#10b981; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <span style="color:white; font-size:16px;">✓</span>
                            </div>
                            <div>
                                <h4 style="margin:0 0 5px; color:#333;">Fast & Free Shipping</h4>
                                <p style="margin:0; color:#666; font-size:14px;">Free shipping on all orders over $50.</p>
                            </div>
                        </div>
                        <div style="display:flex; align-items:flex-start; gap:15px;">
                            <div style="width:28px; height:28px; background:#10b981; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <span style="color:white; font-size:16px;">✓</span>
                            </div>
                            <div>
                                <h4 style="margin:0 0 5px; color:#333;">30-Day Money Back Guarantee</h4>
                                <p style="margin:0; color:#666; font-size:14px;">Not satisfied? Get a full refund, no questions asked.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/></svg>`,
    });

    // Before/After
    bm.add('before-after', {
        label: 'Before/After',
        category: 'Marketing',
        content: `
            <div style="padding:60px 20px; background:#f8f5f0;">
                <div style="max-width:900px; margin:0 auto;">
                    <h2 style="text-align:center; margin-bottom:40px; color:#333;">Real Results</h2>
                    <div style="display:flex; gap:30px; flex-wrap:wrap;">
                        <div style="flex:1; min-width:250px; text-align:center;">
                            <div style="position:relative;">
                                <img src="https://placehold.co/400x400/dc2626/ffffff?text=BEFORE" style="width:100%; border-radius:12px;" />
                                <span style="position:absolute; top:15px; left:15px; background:#dc2626; color:white; padding:6px 16px; border-radius:20px; font-weight:600;">BEFORE</span>
                            </div>
                        </div>
                        <div style="flex:1; min-width:250px; text-align:center;">
                            <div style="position:relative;">
                                <img src="https://placehold.co/400x400/10b981/ffffff?text=AFTER" style="width:100%; border-radius:12px;" />
                                <span style="position:absolute; top:15px; left:15px; background:#10b981; color:white; padding:6px 16px; border-radius:20px; font-weight:600;">AFTER</span>
                            </div>
                        </div>
                    </div>
                    <p style="text-align:center; margin-top:20px; color:#666; font-style:italic;">*Individual results may vary</p>
                </div>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="4" width="8" height="16" rx="1" fill="none" stroke="currentColor" stroke-width="2"/><rect x="14" y="4" width="8" height="16" rx="1" fill="none" stroke="currentColor" stroke-width="2"/><path d="M10 12h4" stroke="currentColor" stroke-width="2"/></svg>`,
    });

    // Pricing Table
    bm.add('pricing-table', {
        label: 'Pricing Table',
        category: 'Marketing',
        content: `
            <div style="padding:60px 20px; background:#fff;">
                <div style="max-width:400px; margin:0 auto; background:linear-gradient(135deg, #1a1714 0%, #2a2520 100%); border-radius:20px; padding:40px; text-align:center; position:relative; overflow:hidden;">
                    <div style="position:absolute; top:20px; right:-30px; background:#C9A227; color:#1a1714; padding:8px 40px; transform:rotate(45deg); font-size:12px; font-weight:700;">BEST VALUE</div>
                    <h3 style="color:#9A7B4F; font-size:14px; text-transform:uppercase; letter-spacing:2px; margin-bottom:10px;">Premium Package</h3>
                    <div style="margin:20px 0;">
                        <span style="color:#666; text-decoration:line-through; font-size:18px;">$199</span>
                        <div style="color:white; font-size:48px; font-weight:700;">$99</div>
                        <span style="color:#9A7B4F;">one-time payment</span>
                    </div>
                    <ul style="text-align:left; list-style:none; padding:0; margin:30px 0; color:#ccc;">
                        <li style="padding:10px 0; border-bottom:1px solid #3d3630;">✓ Premium Formula</li>
                        <li style="padding:10px 0; border-bottom:1px solid #3d3630;">✓ 90-Day Supply</li>
                        <li style="padding:10px 0; border-bottom:1px solid #3d3630;">✓ Free Shipping</li>
                        <li style="padding:10px 0; border-bottom:1px solid #3d3630;">✓ Bonus Guide Included</li>
                        <li style="padding:10px 0;">✓ Priority Support</li>
                    </ul>
                    <button style="width:100%; background:linear-gradient(135deg, #9A7B4F 0%, #C9A227 100%); color:white; border:none; padding:18px; border-radius:30px; font-size:18px; font-weight:700; cursor:pointer;">
                        Order Now →
                    </button>
                    <p style="color:#666; font-size:12px; margin-top:15px;">🔒 Secure checkout • 30-day guarantee</p>
                </div>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="4" y="3" width="16" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><line x1="8" y1="8" x2="16" y2="8" stroke="currentColor" stroke-width="2"/><line x1="8" y1="12" x2="16" y2="12" stroke="currentColor" stroke-width="2"/><line x1="8" y1="16" x2="12" y2="16" stroke="currentColor" stroke-width="2"/></svg>`,
    });

    // FAQ Accordion
    bm.add('faq-accordion', {
        label: 'FAQ Accordion',
        category: 'Marketing',
        content: `
            <div data-faq style="padding:60px 20px; background:#f8f5f0;">
                <div style="max-width:700px; margin:0 auto;">
                    <h2 style="text-align:center; margin-bottom:40px; color:#333;">Frequently Asked Questions</h2>
                    <div style="display:flex; flex-direction:column; gap:15px;">
                        <div data-faq-item style="background:white; border-radius:12px; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                            <div data-faq-trigger style="display:flex; justify-content:space-between; align-items:center; cursor:pointer;" role="button" tabindex="0" aria-expanded="false">
                                <h4 style="margin:0; color:#333;">How long until I see results?</h4>
                                <span data-faq-icon style="color:#9A7B4F; font-size:24px; transition:transform 0.3s;">+</span>
                            </div>
                            <div data-faq-content style="max-height:0; overflow:hidden; transition:max-height 0.3s ease;">
                                <p style="margin:15px 0 0; color:#666; line-height:1.6;">Most customers begin to notice improvements within 2-4 weeks of consistent use. For optimal results, we recommend using the product for at least 90 days.</p>
                            </div>
                        </div>
                        <div data-faq-item style="background:white; border-radius:12px; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                            <div data-faq-trigger style="display:flex; justify-content:space-between; align-items:center; cursor:pointer;" role="button" tabindex="0" aria-expanded="false">
                                <h4 style="margin:0; color:#333;">Is there a money-back guarantee?</h4>
                                <span data-faq-icon style="color:#9A7B4F; font-size:24px; transition:transform 0.3s;">+</span>
                            </div>
                            <div data-faq-content style="max-height:0; overflow:hidden; transition:max-height 0.3s ease;">
                                <p style="margin:15px 0 0; color:#666; line-height:1.6;">Yes! We offer a 30-day money-back guarantee. If you're not completely satisfied, simply contact us for a full refund.</p>
                            </div>
                        </div>
                        <div data-faq-item style="background:white; border-radius:12px; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                            <div data-faq-trigger style="display:flex; justify-content:space-between; align-items:center; cursor:pointer;" role="button" tabindex="0" aria-expanded="false">
                                <h4 style="margin:0; color:#333;">How do I use this product?</h4>
                                <span data-faq-icon style="color:#9A7B4F; font-size:24px; transition:transform 0.3s;">+</span>
                            </div>
                            <div data-faq-content style="max-height:0; overflow:hidden; transition:max-height 0.3s ease;">
                                <p style="margin:15px 0 0; color:#666; line-height:1.6;">Simply follow the instructions included with your order. We recommend taking it once daily with food for best absorption.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/><text x="12" y="16" text-anchor="middle" font-size="12" fill="currentColor">?</text></svg>`,
    });

    // Guarantee Badge
    bm.add('guarantee-badge', {
        label: 'Guarantee Badge',
        category: 'Marketing',
        content: `
            <div style="text-align:center; padding:40px 20px;">
                <div style="display:inline-block; background:linear-gradient(135deg, #f8f5f0 0%, #e8e4de 100%); border:3px solid #9A7B4F; border-radius:50%; width:180px; height:180px; display:inline-flex; flex-direction:column; align-items:center; justify-content:center;">
                    <div style="font-size:36px;">🛡️</div>
                    <div style="font-weight:700; color:#9A7B4F; font-size:18px;">30-DAY</div>
                    <div style="font-weight:600; color:#333; font-size:14px;">MONEY BACK</div>
                    <div style="font-weight:700; color:#9A7B4F; font-size:16px;">GUARANTEE</div>
                </div>
                <p style="color:#666; margin-top:20px; max-width:400px; margin-left:auto; margin-right:auto;">Try it risk-free. If you're not 100% satisfied, we'll refund every penny.</p>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" fill="none" stroke="currentColor" stroke-width="2"/></svg>`,
    });

    // Social Proof Bar
    bm.add('social-proof', {
        label: 'Social Proof Bar',
        category: 'Marketing',
        content: `
            <div style="background:#1a1714; padding:20px;">
                <div style="max-width:1200px; margin:0 auto; display:flex; justify-content:center; align-items:center; gap:40px; flex-wrap:wrap;">
                    <div style="text-align:center;">
                        <div style="color:#C9A227; font-size:14px;">★★★★★</div>
                        <div style="color:#9A7B4F; font-size:12px;">4.9/5 from 2,500+ reviews</div>
                    </div>
                    <div style="color:#3d3630;">|</div>
                    <div style="color:#ccc; font-size:14px;">As Featured In:</div>
                    <div style="display:flex; gap:30px; align-items:center; opacity:0.7;">
                        <span style="color:#ccc; font-weight:600;">Forbes</span>
                        <span style="color:#ccc; font-weight:600;">CNN</span>
                        <span style="color:#ccc; font-weight:600;">NBC</span>
                        <span style="color:#ccc; font-weight:600;">USA Today</span>
                    </div>
                </div>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="none" stroke="currentColor" stroke-width="2"/></svg>`,
    });

    // Testimonial Card
    bm.add('testimonial-card', {
        label: 'Testimonial Card',
        category: 'Social Proof',
        content: `
            <div style="background:white; border-radius:16px; padding:30px; box-shadow:0 4px 20px rgba(0,0,0,0.08); max-width:400px; margin:20px auto;">
                <div style="color:#C9A227; font-size:18px; margin-bottom:15px;">★★★★★</div>
                <p style="color:#333; font-size:16px; line-height:1.7; margin-bottom:20px; font-style:italic;">"Absolutely incredible results! I've tried many products before but nothing comes close to this. Highly recommend to anyone looking for real results."</p>
                <div style="display:flex; align-items:center; gap:15px;">
                    <img src="https://placehold.co/50x50/9A7B4F/ffffff?text=S" style="width:50px; height:50px; border-radius:50%;" />
                    <div>
                        <p style="margin:0; font-weight:600; color:#333;">Sarah M.</p>
                        <p style="margin:0; color:#10b981; font-size:13px;">✓ Verified Buyer</p>
                    </div>
                </div>
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="8" cy="14" r="2" fill="currentColor"/><line x1="12" y1="10" x2="18" y2="10" stroke="currentColor" stroke-width="2"/><line x1="12" y1="14" x2="18" y2="14" stroke="currentColor" stroke-width="2"/></svg>`,
    });

    // Divider
    bm.add('divider', {
        label: 'Divider',
        category: 'Layout',
        content: `
            <div style="padding:30px 20px;">
                <hr style="border:none; height:1px; background:linear-gradient(to right, transparent, #9A7B4F, transparent);" />
            </div>
        `,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><line x1="3" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="2"/></svg>`,
    });

    // Spacer
    bm.add('spacer', {
        label: 'Spacer',
        category: 'Layout',
        content: `<div style="height:60px;"></div>`,
        media: `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="8" y="4" width="8" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="4 2"/></svg>`,
    });
}

// Helper function to clean CSS - removes editor-only placeholder styles
function cleanExportedCss(css) {
    // Remove dashed border placeholder styles (editor visual aids)
    css = css.replace(/border:\s*1px\s+dashed\s+#ccc;?/gi, '');
    // Remove min-height on empty cells (only needed in editor)
    css = css.replace(/min-height:\s*100px;?/gi, '');
    // Remove editor-only selectors (gjs-selected, gjs-hovered)
    css = css.replace(/\.gjs-selected[^}]*}/g, '');
    css = css.replace(/\.gjs-hovered[^}]*}/g, '');
    // Remove empty rules
    css = css.replace(/[^{}]+\{\s*\}/g, '');
    // Clean up extra semicolons and whitespace
    css = css.replace(/\s*;\s*;/g, ';');
    css = css.replace(/{\s*;/g, '{');
    css = css.replace(/;\s*}/g, '}');
    return css.trim();
}

// Helper to clean HTML - removes editor-only data attributes
function cleanExportedHtml(html) {
    html = html.replace(/\s*data-gjs-type="[^"]*"/g, '');
    html = html.replace(/\s*data-highlightable="[^"]*"/g, '');
    // Keep GrapesJS-generated IDs — CSS rules reference them via selectors
    return html;
}

// Helper function to get editor data for saving
window.getGrapesJSData = function() {
    if (!window.gjsEditor) return null;

    const editor = window.gjsEditor;
    const rawCss = editor.getCss();
    const cleanedCss = cleanExportedCss(rawCss);

    return {
        html: cleanExportedHtml(editor.getHtml()),
        css: cleanedCss,
        projectData: editor.getProjectData(),
    };
};

// ===== AUTOSAVE MANAGER =====
class AutosaveManager {
    constructor(editor, options = {}) {
        this.editor = editor;
        this.pageId = options.pageId || 'new';
        this.interval = options.interval || 30000; // 30 seconds
        this.storageKey = `gjs_autosave_${this.pageId}`;
        this.metaKey = `gjs_autosave_meta_${this.pageId}`;
        this.isDirty = false;
        this.lastSaved = null;
        this.timer = null;
        this.onStatusChange = options.onStatusChange || (() => {});

        this.init();
    }

    init() {
        // Debounced dirty marker to avoid rapid-fire from drag-drop
        let dirtyTimeout = null;
        this._debouncedDirty = () => {
            clearTimeout(dirtyTimeout);
            dirtyTimeout = setTimeout(() => this.markDirty(), 150);
        };

        // Listen for changes (store ref for cleanup)
        this.editor.on('component:add component:remove component:update', this._debouncedDirty);
        this.editor.on('style:change', this._debouncedDirty);
        this.editor.on('canvas:drop', this._debouncedDirty);

        // Start autosave timer
        this.startTimer();

        // Save before unload (store ref for cleanup)
        this._beforeUnload = (e) => {
            if (this.isDirty) {
                this.save();
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        };
        window.addEventListener('beforeunload', this._beforeUnload);
    }

    markDirty() {
        if (!this.isDirty) {
            this.isDirty = true;
            this.onStatusChange({ dirty: true, saving: false });
        }
    }

    markClean() {
        this.isDirty = false;
        this.onStatusChange({ dirty: false, saving: false });
    }

    startTimer() {
        if (this.timer) clearInterval(this.timer);
        this.timer = setInterval(() => {
            if (this.isDirty) {
                this.save();
            }
        }, this.interval);
    }

    stopTimer() {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    save() {
        if (this._isSaving) return false;
        this._isSaving = true;

        try {
            this.onStatusChange({ dirty: this.isDirty, saving: true });

            const data = {
                projectData: this.editor.getProjectData(),
                title: document.getElementById('page-title')?.value || '',
                timestamp: Date.now(),
            };

            localStorage.setItem(this.storageKey, JSON.stringify(data.projectData));
            localStorage.setItem(this.metaKey, JSON.stringify({
                title: data.title,
                timestamp: data.timestamp,
                pageId: this.pageId,
            }));

            this.lastSaved = new Date();
            this.isDirty = false;
            this.onStatusChange({ dirty: false, saving: false, lastSaved: this.lastSaved });

            return true;
        } catch (error) {
            if (error.name === 'QuotaExceededError') {
                console.error('LocalStorage full - please save to server');
                this.onStatusChange({ dirty: this.isDirty, saving: false, error: 'Storage full' });
            } else {
                console.error('Autosave failed:', error);
                this.onStatusChange({ dirty: this.isDirty, saving: false, error: true });
            }
            return false;
        } finally {
            this._isSaving = false;
        }
    }

    hasRecoveryData() {
        const meta = this.getRecoveryMeta();
        return meta !== null;
    }

    getRecoveryMeta() {
        try {
            const meta = localStorage.getItem(this.metaKey);
            return meta ? JSON.parse(meta) : null;
        } catch {
            return null;
        }
    }

    getRecoveryData() {
        try {
            const data = localStorage.getItem(this.storageKey);
            return data ? JSON.parse(data) : null;
        } catch {
            return null;
        }
    }

    restore() {
        const data = this.getRecoveryData();
        if (data) {
            this.editor.loadProjectData(data);
            this.markDirty(); // Mark as dirty since it's restored but not saved to server
            return true;
        }
        return false;
    }

    clearRecoveryData() {
        localStorage.removeItem(this.storageKey);
        localStorage.removeItem(this.metaKey);
    }

    // Call this after successful server save
    onServerSave() {
        this.markClean();
        this.clearRecoveryData();
    }

    destroy() {
        this.stopTimer();
        if (this._debouncedDirty) {
            this.editor.off('component:add component:remove component:update', this._debouncedDirty);
            this.editor.off('style:change', this._debouncedDirty);
            this.editor.off('canvas:drop', this._debouncedDirty);
        }
        if (this._beforeUnload) {
            window.removeEventListener('beforeunload', this._beforeUnload);
        }
    }
}

// Toast notification for editor
function showEditorToast(message, type = 'info') {
    const toast = document.createElement('div');
    const bg = type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600';
    toast.className = `fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg text-white z-50 transition-opacity duration-300 ${bg}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
window.showEditorToast = showEditorToast;

// Export autosave manager
window.GrapesJSAutosave = AutosaveManager;

// Helper to format time ago
window.formatTimeAgo = function(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    if (seconds < 60) return 'just now';
    if (seconds < 3600) return `${Math.floor(seconds / 60)} min ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)} hours ago`;
    return new Date(date).toLocaleDateString();
};

// ===== THUMBNAIL CAPTURE =====
/**
 * Capture a thumbnail of the current GrapesJS canvas.
 * Returns a Promise that resolves to a base64 data URL.
 */
window.captureCanvasThumbnail = async function(options = {}) {
    if (!window.gjsEditor) return null;

    const maxWidth = options.maxWidth || 400;
    const maxHeight = options.maxHeight || 300;
    const quality = options.quality || 0.8;
    const timeout = options.timeout || 10000; // 10s timeout

    try {
        const editor = window.gjsEditor;
        const frame = editor.Canvas?.getFrameEl?.();
        if (!frame || !frame.contentDocument) return null;

        const body = frame.contentDocument.body;
        if (!body) return null;

        // Race html2canvas against a timeout
        const capturePromise = html2canvas(body, {
            width: body.scrollWidth,
            height: Math.min(body.scrollHeight, 1500),
            scale: 0.5,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false,
        });

        const timeoutPromise = new Promise((_, reject) =>
            setTimeout(() => reject(new Error('Thumbnail capture timed out')), timeout)
        );

        const canvas = await Promise.race([capturePromise, timeoutPromise]);

        // Resize to thumbnail dimensions
        const thumbCanvas = document.createElement('canvas');
        const aspectRatio = canvas.width / canvas.height;

        if (aspectRatio > maxWidth / maxHeight) {
            thumbCanvas.width = maxWidth;
            thumbCanvas.height = maxWidth / aspectRatio;
        } else {
            thumbCanvas.height = maxHeight;
            thumbCanvas.width = maxHeight * aspectRatio;
        }

        const ctx = thumbCanvas.getContext('2d');
        ctx.drawImage(canvas, 0, 0, thumbCanvas.width, thumbCanvas.height);

        return thumbCanvas.toDataURL('image/jpeg', quality);
    } catch (error) {
        console.error('Failed to capture thumbnail:', error);
        return null;
    }
};
