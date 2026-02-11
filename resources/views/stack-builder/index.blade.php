<x-public-layout
    title="Peptide Stack Builder"
    description="Build your personalized peptide stack based on your health goals. Expert-curated combinations for fat loss, muscle growth, recovery, and more."
>
    <div class="py-14 md:py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @livewire('stack-builder', ['goalSlug' => $goalSlug ?? null])
        </div>
    </div>
</x-public-layout>
