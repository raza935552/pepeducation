<x-public-layout :title="$peptide->name">
    <!-- Hero -->
    <section class="relative bg-gradient-to-br from-brown-900 via-brown-800 to-gold-900/30 text-white py-16 overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%239A7B4F\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('peptides.partials.show-hero')
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12 bg-cream-50 dark:bg-brown-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main -->
                <div class="lg:col-span-2 space-y-6">
                    @include('peptides.partials.show-overview')
                    @include('peptides.partials.show-benefits')
                    @include('peptides.partials.show-timeline')
                    @include('peptides.partials.show-warnings')
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="lg:sticky lg:top-24">
                        @include('peptides.partials.show-quick-stats')
                        @include('peptides.partials.show-molecular')
                    </div>
                </div>
            </div>

            <!-- Related Peptides -->
            @if($relatedPeptides->isNotEmpty())
                @include('peptides.partials.show-related')
            @endif
        </div>
    </section>
</x-public-layout>
