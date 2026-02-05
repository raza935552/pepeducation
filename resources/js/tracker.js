/**
 * ProfessorPeptides Tracker
 * Captures user interactions and sends to backend
 */

class PPTracker {
    constructor(config = {}) {
        this.endpoint = config.endpoint || '/api/tracking';
        this.sessionId = this.getOrCreateSessionId();
        this.pageLoadTime = Date.now();
        this.maxScrollDepth = 0;
        this.events = [];
        this.debounceTimers = {};

        this.init();
    }

    init() {
        this.trackPageView();
        this.setupScrollTracking();
        this.setupClickTracking();
        this.setupErrorTracking();
        this.setupBeforeUnload();
    }

    getOrCreateSessionId() {
        let sessionId = this.getCookie('pp_session_id');
        if (!sessionId) {
            sessionId = this.generateUUID();
            this.setCookie('pp_session_id', sessionId, 365);
        }
        return sessionId;
    }

    generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
            const r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }

    // Cookie helpers
    setCookie(name, value, days) {
        const expires = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
    }

    getCookie(name) {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : null;
    }

    // Track page view
    trackPageView() {
        this.send('page_view', {
            page_url: window.location.href,
            page_title: document.title,
            referrer: document.referrer,
        });
    }

    // Scroll tracking with debounce
    setupScrollTracking() {
        let ticking = false;

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
                    const scrollPercent = Math.round((window.scrollY / scrollHeight) * 100);

                    if (scrollPercent > this.maxScrollDepth) {
                        this.maxScrollDepth = scrollPercent;

                        // Track at milestones: 25%, 50%, 75%, 90%
                        if ([25, 50, 75, 90].includes(scrollPercent)) {
                            this.send('scroll', {
                                scroll_depth: scrollPercent,
                                page_url: window.location.href,
                            });
                        }
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    // Click tracking
    setupClickTracking() {
        document.addEventListener('click', (e) => {
            const target = e.target.closest('a, button, [data-track], [data-cta]');
            if (!target) return;

            const data = {
                element_tag: target.tagName.toLowerCase(),
                element_id: target.id || null,
                element_class: target.className || null,
                element_text: target.textContent?.substring(0, 100).trim(),
                page_url: window.location.href,
            };

            // Get destination URL for links
            let destinationUrl = null;
            if (target.tagName === 'A' && target.href) {
                destinationUrl = target.href;
                data.destination_url = destinationUrl;
            }

            // CTA Click Tracking
            if (this.isCTA(target, destinationUrl)) {
                this.trackCTAClick(target, data, destinationUrl);
            }

            // Track outbound links (external sites)
            if (destinationUrl) {
                try {
                    const isExternal = new URL(destinationUrl).origin !== window.location.origin;
                    if (isExternal) {
                        this.send('outbound_click', data);
                    }
                } catch (e) {}
            }

            // Track rage clicks
            this.detectRageClick(e, data);

            this.send('click', data);
        });
    }

    // Detect if element is a CTA
    isCTA(element, destinationUrl) {
        // Explicit data-cta attribute
        if (element.hasAttribute('data-cta')) {
            return true;
        }

        // Auto-detect by destination URL patterns
        if (destinationUrl) {
            const ctaPatterns = [
                '/quiz/',           // Quiz pages
                '/go/',             // Outbound tracked links
                'fastpeptix.com',   // Shop links
                '#subscribe',       // Subscribe anchors
                '#signup',          // Signup anchors
            ];
            return ctaPatterns.some(pattern => destinationUrl.includes(pattern));
        }

        // Auto-detect by button text
        const text = element.textContent?.toLowerCase() || '';
        const ctaTexts = [
            'take the quiz', 'get started', 'shop now', 'buy now',
            'subscribe', 'sign up', 'download', 'get access',
            'find my', 'find your', 'learn more', 'start now'
        ];
        return ctaTexts.some(cta => text.includes(cta));
    }

    // Track CTA click with detailed info
    trackCTAClick(element, baseData, destinationUrl) {
        const ctaData = {
            ...baseData,
            cta_name: element.getAttribute('data-cta-name') || element.textContent?.substring(0, 50).trim(),
            cta_type: this.getCTAType(element, destinationUrl),
            cta_position: this.getCTAPosition(element),
            source_page: window.location.pathname,
            destination: destinationUrl || null,
        };

        this.send('cta_click', ctaData);
    }

    // Determine CTA type
    getCTAType(element, destinationUrl) {
        if (element.getAttribute('data-cta-type')) {
            return element.getAttribute('data-cta-type');
        }
        if (destinationUrl?.includes('/quiz/')) return 'quiz';
        if (destinationUrl?.includes('/go/')) return 'outbound';
        if (destinationUrl?.includes('fastpeptix')) return 'shop';
        if (destinationUrl?.includes('#subscribe')) return 'subscribe';
        return 'general';
    }

    // Get CTA position on page
    getCTAPosition(element) {
        const rect = element.getBoundingClientRect();
        const viewportHeight = window.innerHeight;
        const scrollY = window.scrollY;
        const elementTop = rect.top + scrollY;
        const pageHeight = document.documentElement.scrollHeight;

        // Calculate position as percentage of page
        const positionPercent = Math.round((elementTop / pageHeight) * 100);

        if (positionPercent < 20) return 'hero';
        if (positionPercent < 50) return 'upper';
        if (positionPercent < 80) return 'middle';
        return 'footer';
    }

    // Rage click detection
    detectRageClick(event, data) {
        const key = 'rage_' + Math.round(event.clientX / 50) + '_' + Math.round(event.clientY / 50);

        if (!this.debounceTimers[key]) {
            this.debounceTimers[key] = { count: 0, timer: null };
        }

        this.debounceTimers[key].count++;
        clearTimeout(this.debounceTimers[key].timer);

        this.debounceTimers[key].timer = setTimeout(() => {
            if (this.debounceTimers[key].count >= 3) {
                this.send('rage_click', {
                    ...data,
                    click_count: this.debounceTimers[key].count,
                });
            }
            delete this.debounceTimers[key];
        }, 1000);
    }

    // Error tracking
    setupErrorTracking() {
        window.addEventListener('error', (e) => {
            this.send('js_error', {
                error_message: e.message,
                error_file: e.filename,
                error_line: e.lineno,
                error_column: e.colno,
                page_url: window.location.href,
            });
        });

        window.addEventListener('unhandledrejection', (e) => {
            this.send('js_error', {
                error_message: e.reason?.message || 'Unhandled Promise Rejection',
                error_type: 'promise_rejection',
                page_url: window.location.href,
            });
        });
    }

    // Before unload - send time on page
    setupBeforeUnload() {
        window.addEventListener('beforeunload', () => {
            const timeOnPage = Math.round((Date.now() - this.pageLoadTime) / 1000);

            this.sendBeacon('page_exit', {
                time_on_page: timeOnPage,
                max_scroll_depth: this.maxScrollDepth,
                page_url: window.location.href,
            });
        });
    }

    // Send event to backend
    send(eventType, data) {
        const payload = {
            event_type: eventType,
            session_id: this.sessionId,
            timestamp: new Date().toISOString(),
            ...data,
        };

        fetch(this.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify(payload),
        }).catch(() => {});
    }

    // Use beacon API for exit events
    sendBeacon(eventType, data) {
        const payload = {
            event_type: eventType,
            session_id: this.sessionId,
            timestamp: new Date().toISOString(),
            ...data,
        };

        navigator.sendBeacon(this.endpoint, JSON.stringify(payload));
    }

    // Public methods for custom tracking
    trackEvent(name, properties = {}) {
        this.send(name, { ...properties, page_url: window.location.href });
    }

    trackQuizStart(quizId) {
        this.send('quiz_start', { quiz_id: quizId });
    }

    trackQuizAnswer(quizId, questionId, answer) {
        this.send('quiz_answer', { quiz_id: quizId, question_id: questionId, answer });
    }

    trackQuizComplete(quizId, outcome) {
        this.send('quiz_complete', { quiz_id: quizId, outcome });
    }

    trackPopupView(popupId) {
        this.send('popup_view', { popup_id: popupId });
    }

    trackPopupConvert(popupId) {
        this.send('popup_convert', { popup_id: popupId });
    }

    identifyUser(email) {
        this.setCookie('pp_email', email, 365);
        this.send('identify', { email });
    }
}

// Initialize tracker
window.PPTracker = new PPTracker();
