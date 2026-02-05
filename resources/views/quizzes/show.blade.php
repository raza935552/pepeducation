<x-public-layout>
    <x-slot:title>{{ $quiz->title }}</x-slot:title>
    <x-slot:description>{{ $quiz->description }}</x-slot:description>

    <div class="min-h-screen bg-gradient-to-b from-cream-50 to-white py-12 px-4">
        <div class="max-w-2xl mx-auto">
            <!-- Quiz Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $quiz->title }}</h1>
                @if($quiz->description)
                    <p class="text-lg text-gray-600">{{ $quiz->description }}</p>
                @endif
            </div>

            <!-- Quiz Player Component -->
            <livewire:quiz-player :quiz="$quiz" />
        </div>
    </div>
</x-public-layout>
