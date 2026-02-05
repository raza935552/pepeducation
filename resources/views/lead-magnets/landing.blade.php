<x-public-layout>
    <x-slot name="title">{{ $leadMagnet->landing_headline ?? $leadMagnet->name }}</x-slot>

    <div class="min-h-screen bg-gradient-to-b from-cream-100 to-cream-50">
        <!-- Hero Section -->
        <div class="bg-gradient-to-br from-brown-800 to-brown-900 text-white py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                @if($leadMagnet->segment && $leadMagnet->segment !== 'all')
                    <span class="inline-block px-3 py-1 bg-gold-500/20 text-gold-400 text-sm font-medium rounded-full mb-4">
                        FREE {{ strtoupper($leadMagnet->file_type ?? 'PDF') }}
                    </span>
                @endif

                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
                    {{ $leadMagnet->landing_headline ?? $leadMagnet->name }}
                </h1>

                @if($leadMagnet->landing_description)
                    <p class="text-lg md:text-xl text-gray-300 max-w-2xl mx-auto">
                        {{ $leadMagnet->landing_description }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8">
            <div class="grid md:grid-cols-2 gap-8 items-start">
                <!-- Preview/Image Column -->
                <div class="bg-white rounded-2xl shadow-xl p-6 order-2 md:order-1">
                    @if($leadMagnet->preview_image)
                        <img src="{{ Storage::url($leadMagnet->preview_image) }}"
                             alt="{{ $leadMagnet->name }}"
                             class="w-full rounded-lg shadow-lg">
                    @elseif($leadMagnet->thumbnail)
                        <img src="{{ Storage::url($leadMagnet->thumbnail) }}"
                             alt="{{ $leadMagnet->name }}"
                             class="w-full rounded-lg shadow-lg">
                    @else
                        <div class="aspect-[3/4] bg-gradient-to-br from-gold-100 to-gold-200 rounded-lg flex items-center justify-center">
                            <div class="text-center p-8">
                                <svg class="w-20 h-20 mx-auto text-gold-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span class="text-gold-700 font-bold text-xl">{{ strtoupper($leadMagnet->file_type ?? 'PDF') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($leadMagnet->description)
                        <p class="mt-4 text-gray-600 text-sm">
                            {{ $leadMagnet->description }}
                        </p>
                    @endif

                    @if($leadMagnet->file_size)
                        <p class="mt-2 text-gray-400 text-xs">
                            File size: {{ $leadMagnet->getFileSizeFormatted() }}
                        </p>
                    @endif
                </div>

                <!-- Download Form Column -->
                <div class="bg-white rounded-2xl shadow-xl p-6 order-1 md:order-2">
                    @if(session('success'))
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
                            <p class="text-gray-600">{{ session('success') }}</p>
                        </div>
                    @else
                        <h2 class="text-xl font-bold text-gray-900 mb-4">
                            Get Your Free {{ strtoupper($leadMagnet->file_type ?? 'PDF') }}
                        </h2>

                        @livewire('lead-magnet-form', ['leadMagnet' => $leadMagnet])
                    @endif

                    <!-- Benefits List -->
                    @if($leadMagnet->landing_benefits && count($leadMagnet->landing_benefits) > 0)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">What you'll learn:</h3>
                            <ul class="space-y-2">
                                @foreach($leadMagnet->landing_benefits as $benefit)
                                    <li class="flex items-start gap-2 text-sm text-gray-600">
                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>{{ $benefit }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Trust Elements -->
            <div class="mt-12 text-center pb-16">
                <div class="flex flex-wrap justify-center gap-6 text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>No spam, ever</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Your info is safe</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span>Instant download</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
