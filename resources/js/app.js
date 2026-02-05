import './bootstrap';

// Alpine.js - Only initialize if not already loaded by Livewire
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';

// Check if Alpine is already initialized (by Livewire)
if (!window.Alpine) {
    Alpine.plugin(focus);
    Alpine.plugin(intersect);
    window.Alpine = Alpine;
    Alpine.start();
}

// Dark mode disabled - site always uses light mode
// Keep the object for backwards compatibility but it does nothing
window.darkMode = {
    toggle() {},
    init() {
        // Always ensure light mode
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

// Ensure light mode on page load
window.darkMode.init();

// ProfessorPeptides Tracker
import './tracker';
