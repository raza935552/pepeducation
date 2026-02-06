/**
 * Runtime JS for GrapesJS page components (carousels, FAQ accordions)
 * Auto-initializes on DOMContentLoaded
 */

function initCarousels() {
    document.querySelectorAll('[data-carousel]').forEach(container => {
        const track = container.querySelector('[data-carousel-track]');
        const slides = container.querySelectorAll('[data-carousel-slide]');
        const dotsContainer = container.querySelector('[data-carousel-dots]');
        const prevBtn = container.querySelector('[data-carousel-prev]');
        const nextBtn = container.querySelector('[data-carousel-next]');
        if (!track || slides.length === 0) return;

        let current = 0;
        const total = slides.length;
        const autoplayMs = parseInt(container.dataset.autoplay) || 0;
        let autoplayTimer = null;

        // Build dots
        if (dotsContainer) {
            dotsContainer.innerHTML = '';
            slides.forEach((_, i) => {
                const dot = document.createElement('button');
                dot.style.cssText = 'width:10px;height:10px;border-radius:50%;border:none;cursor:pointer;padding:0;';
                dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                dot.addEventListener('click', () => goTo(i));
                dotsContainer.appendChild(dot);
            });
        }

        function goTo(index) {
            current = ((index % total) + total) % total;
            track.style.transform = `translateX(-${current * 100}%)`;
            updateDots();
            resetAutoplay();
        }

        function updateDots() {
            if (!dotsContainer) return;
            dotsContainer.querySelectorAll('button').forEach((dot, i) => {
                dot.style.background = i === current ? '#9A7B4F' : 'rgba(255,255,255,0.5)';
            });
        }

        function resetAutoplay() {
            if (!autoplayMs) return;
            clearInterval(autoplayTimer);
            autoplayTimer = setInterval(() => goTo(current + 1), autoplayMs);
        }

        if (prevBtn) prevBtn.addEventListener('click', () => goTo(current - 1));
        if (nextBtn) nextBtn.addEventListener('click', () => goTo(current + 1));

        // Touch/swipe support
        let touchStartX = 0;
        container.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
        container.addEventListener('touchend', e => {
            const diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) goTo(current + (diff > 0 ? 1 : -1));
        }, { passive: true });

        updateDots();
        if (autoplayMs) autoplayTimer = setInterval(() => goTo(current + 1), autoplayMs);
    });
}

function initFaqAccordions() {
    document.querySelectorAll('[data-faq]').forEach(faq => {
        faq.querySelectorAll('[data-faq-trigger]').forEach(trigger => {
            const item = trigger.closest('[data-faq-item]');
            if (!item) return;
            const content = item.querySelector('[data-faq-content]');
            const icon = trigger.querySelector('[data-faq-icon]');
            if (!content) return;

            function toggle() {
                const isOpen = trigger.getAttribute('aria-expanded') === 'true';
                // Close all siblings first
                faq.querySelectorAll('[data-faq-trigger]').forEach(t => {
                    t.setAttribute('aria-expanded', 'false');
                    const c = t.closest('[data-faq-item]')?.querySelector('[data-faq-content]');
                    const ic = t.querySelector('[data-faq-icon]');
                    if (c) c.style.maxHeight = '0';
                    if (ic) ic.textContent = '+';
                });
                // Open clicked if was closed
                if (!isOpen) {
                    trigger.setAttribute('aria-expanded', 'true');
                    content.style.maxHeight = content.scrollHeight + 'px';
                    if (icon) icon.textContent = '−';
                }
            }

            trigger.addEventListener('click', toggle);
            trigger.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); }
            });
        });
    });
}

function initCountdownTimers() {
    document.querySelectorAll('[data-countdown]').forEach((el, index) => {
        const mode = el.dataset.countdownMode || 'fixed';
        const expiredMsg = el.dataset.countdownExpired || 'Offer has expired!';
        const timerId = el.dataset.countdownId || `cd_${index}`;

        let targetDate;

        if (mode === 'evergreen') {
            const minutes = parseInt(el.dataset.countdownEvergreen) || 60;
            const storageKey = `pp_cd_${location.pathname}_${timerId}`;
            const saved = localStorage.getItem(storageKey);

            if (saved) {
                targetDate = new Date(parseInt(saved));
            } else {
                targetDate = new Date(Date.now() + minutes * 60 * 1000);
                localStorage.setItem(storageKey, targetDate.getTime().toString());
            }
        } else {
            const dateStr = el.dataset.countdownDate;
            if (!dateStr) return;
            targetDate = new Date(dateStr);
        }

        if (isNaN(targetDate.getTime())) return;

        const daysEl = el.querySelector('[data-cd-days]');
        const hoursEl = el.querySelector('[data-cd-hours]');
        const minsEl = el.querySelector('[data-cd-mins]');
        const secsEl = el.querySelector('[data-cd-secs]');
        const expiredEl = el.querySelector('[data-cd-expired]');

        function update() {
            const diff = targetDate.getTime() - Date.now();

            if (diff <= 0) {
                if (daysEl) daysEl.textContent = '00';
                if (hoursEl) hoursEl.textContent = '00';
                if (minsEl) minsEl.textContent = '00';
                if (secsEl) secsEl.textContent = '00';
                if (expiredEl) {
                    expiredEl.textContent = expiredMsg;
                    expiredEl.style.display = '';
                }
                clearInterval(timer);
                return;
            }

            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);

            if (daysEl) daysEl.textContent = String(d).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(h).padStart(2, '0');
            if (minsEl) minsEl.textContent = String(m).padStart(2, '0');
            if (secsEl) secsEl.textContent = String(s).padStart(2, '0');
        }

        update();
        const timer = setInterval(update, 1000);
    });
}

function initExitIntentPopups() {
    document.querySelectorAll('[data-exit-intent]').forEach(popup => {
        const id = popup.dataset.exitId || 'exit-default';
        const showOnce = popup.dataset.exitOnce === 'true';
        const delay = parseInt(popup.dataset.exitDelay) || 0;
        const storageKey = `pp_exit_${id}`;

        if (showOnce && localStorage.getItem(storageKey)) return;

        let shown = false;
        let ready = false;

        setTimeout(() => { ready = true; }, Math.max(delay, 1000));

        document.addEventListener('mouseout', function handler(e) {
            if (!ready || shown) return;
            if (e.clientY > 5) return;
            shown = true;
            popup.style.display = 'flex';
            if (showOnce) localStorage.setItem(storageKey, '1');
            document.removeEventListener('mouseout', handler);
        });

        // Close handlers
        popup.querySelectorAll('[data-exit-close]').forEach(btn => {
            btn.addEventListener('click', () => { popup.style.display = 'none'; });
        });
        popup.addEventListener('click', (e) => {
            if (e.target === popup) popup.style.display = 'none';
        });
    });
}

function initStickyCtas() {
    document.querySelectorAll('[data-sticky-cta]').forEach(bar => {
        const scrollThreshold = parseInt(bar.dataset.stickyScroll) || 30;
        const closable = bar.dataset.stickyClosable === 'true';
        const id = bar.dataset.stickyId || 'sticky-default';
        const storageKey = `pp_sticky_closed_${id}`;

        if (closable && sessionStorage.getItem(storageKey)) return;

        let visible = false;

        window.addEventListener('scroll', () => {
            const pct = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
            if (pct >= scrollThreshold && !visible) {
                visible = true;
                bar.style.display = 'block';
            } else if (pct < scrollThreshold && visible) {
                visible = false;
                bar.style.display = 'none';
            }
        }, { passive: true });

        if (closable) {
            bar.querySelectorAll('[data-sticky-close]').forEach(btn => {
                btn.addEventListener('click', () => {
                    bar.style.display = 'none';
                    sessionStorage.setItem(storageKey, '1');
                });
            });
        }
    });
}

function initBuilderForms() {
    document.querySelectorAll('[data-builder-form]').forEach(form => {
        const formName = form.dataset.formName || 'default';
        const successMsg = form.dataset.formSuccess || 'Thanks! Submitted successfully.';
        const submitBtn = form.querySelector('[data-form-submit]');
        const statusEl = form.querySelector('[data-form-status]');
        if (!submitBtn) return;

        submitBtn.addEventListener('click', async () => {
            const inputs = form.querySelectorAll('input[name], textarea[name], select[name]');
            const fields = {};
            let valid = true;

            inputs.forEach(input => {
                if (input.required && !input.value.trim()) { valid = false; input.style.borderColor = '#ef4444'; }
                else { input.style.borderColor = '#e8e4de'; }
                if (input.name) fields[input.name] = input.value;
            });

            if (!valid) {
                showStatus(statusEl, 'Please fill in all required fields.', false);
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
            const slug = location.pathname.replace(/^\//, '');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

            try {
                const r = await fetch('/form-submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ form_name: formName, page_slug: slug, fields }),
                });
                if (r.ok) {
                    showStatus(statusEl, successMsg, true);
                    inputs.forEach(i => { i.value = ''; });
                } else {
                    showStatus(statusEl, 'Something went wrong. Please try again.', false);
                }
            } catch {
                showStatus(statusEl, 'Network error. Please try again.', false);
            }
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
        });
    });
}

function showStatus(el, msg, success) {
    if (!el) return;
    el.style.display = 'block';
    el.style.background = success ? '#d1fae5' : '#fee2e2';
    el.style.color = success ? '#065f46' : '#991b1b';
    el.textContent = msg;
    setTimeout(() => { el.style.display = 'none'; }, 5000);
}

function initAnimations() {
    // Inject animation CSS keyframes
    if (!document.getElementById('pp-anim-styles')) {
        const style = document.createElement('style');
        style.id = 'pp-anim-styles';
        style.textContent = `
            [data-anim] { opacity: 0; transition: opacity 0.6s ease, transform 0.6s ease; }
            [data-anim].pp-anim-active { opacity: 1 !important; transform: none !important; }
            [data-anim="fade-in"] { transform: none; }
            [data-anim="slide-up"] { transform: translateY(40px); }
            [data-anim="slide-down"] { transform: translateY(-40px); }
            [data-anim="slide-left"] { transform: translateX(40px); }
            [data-anim="slide-right"] { transform: translateX(-40px); }
            [data-anim="zoom-in"] { transform: scale(0.8); }
            [data-anim="zoom-out"] { transform: scale(1.2); }
            [data-anim="flip-x"] { transform: rotateY(90deg); }
            [data-anim="bounce"] { transform: translateY(30px); }
            @keyframes pp-bounce { 0%,20%,50%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-20px)} 60%{transform:translateY(-10px)} }
            [data-anim="bounce"].pp-anim-active { animation: pp-bounce 0.8s ease; }
        `;
        document.head.appendChild(style);
    }

    const animEls = document.querySelectorAll('[data-anim]');
    if (!animEls.length) return;

    animEls.forEach(el => {
        const dur = el.dataset.animDuration || '600';
        const delay = el.dataset.animDelay || '0';
        el.style.transitionDuration = dur + 'ms';
        el.style.transitionDelay = delay + 'ms';

        const trigger = el.dataset.animTrigger || 'scroll';

        if (trigger === 'load') {
            setTimeout(() => el.classList.add('pp-anim-active'), parseInt(delay));
        } else if (trigger === 'hover') {
            el.style.opacity = '1';
            el.style.transform = 'none';
            el.addEventListener('mouseenter', () => el.classList.add('pp-anim-active'));
            el.addEventListener('mouseleave', () => el.classList.remove('pp-anim-active'));
        }
        // scroll handled by observer below
    });

    // IntersectionObserver for scroll-triggered animations
    const scrollEls = document.querySelectorAll('[data-anim]:not([data-anim-trigger="load"]):not([data-anim-trigger="hover"])');
    if (scrollEls.length && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('pp-anim-active');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        scrollEls.forEach(el => observer.observe(el));
    }
}

function initCalculators() {
    document.querySelectorAll('[data-calculator]').forEach(calc => {
        const btn = calc.querySelector('[data-calc-btn]');
        const result = calc.querySelector('[data-calc-result]');
        if (!btn || !result) return;

        btn.addEventListener('click', () => {
            const inputs = calc.querySelectorAll('[data-calc-input]');
            const formula = calc.dataset.calcFormula || 'sum';
            const values = [];
            inputs.forEach(inp => values.push(parseFloat(inp.value) || 0));

            let output = 0;
            if (formula === 'sum') output = values.reduce((a, b) => a + b, 0);
            else if (formula === 'bmi' && values.length >= 2) output = values[0] / ((values[1] / 100) ** 2);
            else if (formula === 'dosage' && values.length >= 2) output = values[0] * values[1];
            else output = values.reduce((a, b) => a + b, 0);

            result.textContent = isNaN(output) ? 'Error' : output.toFixed(2);
            result.style.display = 'block';
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initCarousels();
    initFaqAccordions();
    initCountdownTimers();
    initExitIntentPopups();
    initStickyCtas();
    initBuilderForms();
    initAnimations();
    initCalculators();
});
