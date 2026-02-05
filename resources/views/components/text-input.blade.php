@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-cream-300 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 focus:border-gold-500 focus:ring-gold-500 rounded-xl shadow-sm']) }}>
