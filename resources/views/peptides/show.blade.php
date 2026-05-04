<x-public-layout
    :title="$peptide->meta_title ?? $peptide->name . ' - Benefits, Dosing & Protocols'"
    :description="$peptide->meta_description ?? Str::limit(strip_tags($peptide->overview), 155)"
    :canonical="route('peptides.show', $peptide->slug)"
>
    @push('head')
        @include('partials.schema-peptide')
        @include('partials.schema-faq')
        @include('partials.schema-peptide-howto')
    @endpush

    <!-- Hero -->
    <section class="relative bg-gradient-to-br from-dark-900 via-dark-800 to-primary-900/30 text-white py-16 overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%239A7B4F\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('peptides.partials.show-hero')
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12 bg-surface-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main -->
                <div class="lg:col-span-2 space-y-6">
                    @include('peptides.partials.show-overview')
                    @include('peptides.partials.show-benefits')
                    @include('peptides.partials.show-protocols')
                    @include('peptides.partials.show-compatible-peptides')
                    @include('peptides.partials.show-reconstitution')
                    @include('peptides.partials.show-quality-indicators')
                    @include('peptides.partials.show-effectiveness')
                    @include('peptides.partials.show-timeline')
                    @include('peptides.partials.show-warnings')
                    @include('peptides.partials.show-references')
                    <x-buy-cta :peptide="$peptide" context="peptide-bottom" variant="banner" />
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="lg:sticky lg:top-24 space-y-6">
                        <x-buy-cta :peptide="$peptide" context="peptide-sidebar" variant="card" />
                        @include('peptides.partials.show-quick-stats')
                        @include('peptides.partials.show-molecular')
                    </div>
                </div>
            </div>

            <!-- Related Peptides -->
            @if($relatedPeptides->isNotEmpty())
                @include('peptides.partials.show-related')
            @endif

            <!-- Related Articles -->
            @include('peptides.partials.show-related-posts')
        </div>
    </section>
    @push('scripts')
    <script>
    (function() {
        // Use global PepMarketing (set up by customerio-tracking component)
        if (window.PepMarketing) {
            PepMarketing.track('Viewed Product', {
                ProductName: '{{ addslashes($peptide->name) }}',
                ProductID: '{{ $peptide->slug }}',
                Categories: {!! json_encode($peptide->categories->pluck('name')) !!},
                URL: window.location.href
            });
        } else if (window._cio) {
            _cio.track('Viewed Product', {
                ProductName: '{{ addslashes($peptide->name) }}',
                ProductID: '{{ $peptide->slug }}',
                Categories: {!! json_encode($peptide->categories->pluck('name')) !!},
                URL: window.location.href
            });
        }
    })();
    </script>
    @endpush
</x-public-layout>
