import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                // Admin-only accent (sky blue) – immune to theme changes
                'admin-primary': {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                    950: '#082f49',
                },

                // Theme-controlled colors (driven by CSS variables)
                primary: {
                    DEFAULT: 'rgb(var(--primary) / <alpha-value>)',
                    50: 'rgb(var(--primary-50) / <alpha-value>)',
                    100: 'rgb(var(--primary-100) / <alpha-value>)',
                    200: 'rgb(var(--primary-200) / <alpha-value>)',
                    300: 'rgb(var(--primary-300) / <alpha-value>)',
                    400: 'rgb(var(--primary-400) / <alpha-value>)',
                    500: 'rgb(var(--primary-500) / <alpha-value>)',
                    600: 'rgb(var(--primary-600) / <alpha-value>)',
                    700: 'rgb(var(--primary-700) / <alpha-value>)',
                    800: 'rgb(var(--primary-800) / <alpha-value>)',
                    900: 'rgb(var(--primary-900) / <alpha-value>)',
                    950: 'rgb(var(--primary-950) / <alpha-value>)',
                },
                secondary: {
                    DEFAULT: 'rgb(var(--secondary) / <alpha-value>)',
                    50: 'rgb(var(--secondary-50) / <alpha-value>)',
                    100: 'rgb(var(--secondary-100) / <alpha-value>)',
                    200: 'rgb(var(--secondary-200) / <alpha-value>)',
                    300: 'rgb(var(--secondary-300) / <alpha-value>)',
                    400: 'rgb(var(--secondary-400) / <alpha-value>)',
                    500: 'rgb(var(--secondary-500) / <alpha-value>)',
                    600: 'rgb(var(--secondary-600) / <alpha-value>)',
                    700: 'rgb(var(--secondary-700) / <alpha-value>)',
                    800: 'rgb(var(--secondary-800) / <alpha-value>)',
                    900: 'rgb(var(--secondary-900) / <alpha-value>)',
                    950: 'rgb(var(--secondary-950) / <alpha-value>)',
                },
                surface: {
                    DEFAULT: 'rgb(var(--bg) / <alpha-value>)',
                    50: 'rgb(var(--bg-50) / <alpha-value>)',
                    100: 'rgb(var(--bg-100) / <alpha-value>)',
                    200: 'rgb(var(--bg-200) / <alpha-value>)',
                    300: 'rgb(var(--bg-300) / <alpha-value>)',
                    400: 'rgb(var(--bg-400) / <alpha-value>)',
                    500: 'rgb(var(--bg-500) / <alpha-value>)',
                    600: 'rgb(var(--bg-600) / <alpha-value>)',
                    700: 'rgb(var(--bg-700) / <alpha-value>)',
                    800: 'rgb(var(--bg-800) / <alpha-value>)',
                    900: 'rgb(var(--bg-900) / <alpha-value>)',
                    950: 'rgb(var(--bg-950) / <alpha-value>)',
                },
                dark: {
                    DEFAULT: 'rgb(var(--dark) / <alpha-value>)',
                    50: 'rgb(var(--dark-50) / <alpha-value>)',
                    100: 'rgb(var(--dark-100) / <alpha-value>)',
                    200: 'rgb(var(--dark-200) / <alpha-value>)',
                    300: 'rgb(var(--dark-300) / <alpha-value>)',
                    400: 'rgb(var(--dark-400) / <alpha-value>)',
                    500: 'rgb(var(--dark-500) / <alpha-value>)',
                    600: 'rgb(var(--dark-600) / <alpha-value>)',
                    700: 'rgb(var(--dark-700) / <alpha-value>)',
                    800: 'rgb(var(--dark-800) / <alpha-value>)',
                    900: 'rgb(var(--dark-900) / <alpha-value>)',
                    950: 'rgb(var(--dark-950) / <alpha-value>)',
                },

                // Brand palette
                cream: {
                    50: '#FDFCFA',
                    100: '#F5F1EB',
                    200: '#E8E4DE',
                    300: '#D4CFC7',
                    400: '#B8B0A5',
                    500: '#9C9285',
                },
                gold: {
                    50: '#FBF7EE',
                    100: '#F0E6D0',
                    300: '#D4B861',
                    400: '#C9A227',
                    500: '#9A7B4F',
                    600: '#7A5F3D',
                    700: '#5A452D',
                    900: '#3D2E1E',
                },
                caramel: {
                    400: '#BF9270',
                    500: '#A67B5B',
                    600: '#8B6347',
                    700: '#704F38',
                },
                brown: {
                    600: '#4A433C',
                    700: '#3D3630',
                    800: '#2A2520',
                    900: '#1A1714',
                    950: '#0F0D0B',
                },
                'brand-gold': '#9A7B4F',

                // Flat semantic colors
                heading: 'rgb(var(--heading) / <alpha-value>)',
                body: 'rgb(var(--text) / <alpha-value>)',

                // Section-specific overrides (flat, no shades)
                'header-bg':     'rgb(var(--header-bg) / <alpha-value>)',
                'header-text':   'rgb(var(--header-text) / <alpha-value>)',
                'nav-link':      'rgb(var(--nav-link) / <alpha-value>)',
                'nav-active':    'rgb(var(--nav-active) / <alpha-value>)',
                'btn-primary-bg':   'rgb(var(--btn-primary-bg) / <alpha-value>)',
                'btn-primary-text': 'rgb(var(--btn-primary-text) / <alpha-value>)',
                'btn-accent-bg':    'rgb(var(--btn-accent-bg) / <alpha-value>)',
                'btn-accent-text':  'rgb(var(--btn-accent-text) / <alpha-value>)',
                'link':          'rgb(var(--link-color) / <alpha-value>)',
                'link-hover':    'rgb(var(--link-hover) / <alpha-value>)',
                'footer-bg':     'rgb(var(--footer-bg) / <alpha-value>)',
                'footer-text':   'rgb(var(--footer-text) / <alpha-value>)',
                'footer-heading':'rgb(var(--footer-heading) / <alpha-value>)',
                'footer-link':   'rgb(var(--footer-link) / <alpha-value>)',
                'footer-link-hover': 'rgb(var(--footer-link-hover) / <alpha-value>)',
            },
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-out',
                'fade-out': 'fadeOut 0.3s ease-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
                'spin-slow': 'spin 3s linear infinite',
                'search-glow': 'searchGlow 3s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeOut: {
                    '0%': { opacity: '1' },
                    '100%': { opacity: '0' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                searchGlow: {
                    '0%, 100%': { boxShadow: '0 25px 50px -12px rgba(0,0,0,0.25), 0 0 0 0px rgb(var(--primary-500) / 0)' },
                    '50%': { boxShadow: '0 25px 50px -12px rgba(0,0,0,0.25), 0 0 0 4px rgb(var(--primary-500) / 0.15)' },
                },
            },
        },
    },
    plugins: [forms, typography],
};
