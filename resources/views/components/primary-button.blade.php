<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-gray-900 dark:bg-cream-100 border border-transparent rounded-full font-semibold text-sm text-white dark:text-brown-900 uppercase tracking-widest hover:bg-gray-800 dark:hover:bg-cream-200 focus:bg-gray-800 dark:focus:bg-cream-200 active:bg-gray-900 dark:active:bg-cream-300 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:ring-offset-2 dark:focus:ring-offset-brown-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
