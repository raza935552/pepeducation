@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-cream-300 focus:border-gold-500 focus:ring-gold-500 rounded-xl shadow-sm']) }}>
