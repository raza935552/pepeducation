<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>SEO Review — All Pages</span>
            <a href="{{ route('admin.settings.seo') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to SEO Settings
            </a>
        </div>
    </x-slot>

    <div x-data="seoReview()" class="space-y-6">

        {{-- Stats --}}
        @php
            $totalPeptides = $peptides->count();
            $peptidesSeo = $peptides->filter(fn($p) => $p->meta_title && $p->meta_description)->count();
            $totalBlog = $blogPosts->count();
            $blogSeo = $blogPosts->filter(fn($p) => $p->meta_title && $p->meta_description)->count();
            $totalPages = $pages->count();
            $pagesSeo = $pages->filter(fn($p) => $p->meta_title && $p->meta_description)->count();
            $totalStatic = count($staticPages);
            $staticSeo = collect($staticPages)->filter(fn($s) => !empty($s['meta_title']) && !empty($s['meta_description']))->count();
            $totalAll = $totalPeptides + $totalBlog + $totalPages + $totalStatic;
            $totalSeo = $peptidesSeo + $blogSeo + $pagesSeo + $staticSeo;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="card p-4 text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $totalAll }}</div>
                <div class="text-xs text-gray-500">Total Pages</div>
            </div>
            <div class="card p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $totalSeo }}</div>
                <div class="text-xs text-gray-500">Has SEO</div>
            </div>
            <div class="card p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $totalAll - $totalSeo }}</div>
                <div class="text-xs text-gray-500">Missing SEO</div>
            </div>
            <div class="card p-4 text-center">
                <div class="text-2xl font-bold" :class="titleIssues + descIssues > 0 ? 'text-yellow-600' : 'text-green-600'" x-text="titleIssues + descIssues"></div>
                <div class="text-xs text-gray-500">Length Issues</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap gap-2">
            <button @click="setFilter('all')" class="px-3 py-1.5 text-xs font-medium rounded-lg transition" :class="filter === 'all' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">All</button>
            <button @click="setFilter('missing')" class="px-3 py-1.5 text-xs font-medium rounded-lg transition" :class="filter === 'missing' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">Missing SEO</button>
            <button @click="setFilter('issues')" class="px-3 py-1.5 text-xs font-medium rounded-lg transition" :class="filter === 'issues' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">Length Issues</button>
            <button @click="setFilter('good')" class="px-3 py-1.5 text-xs font-medium rounded-lg transition" :class="filter === 'good' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">Good</button>
            <span class="border-l border-gray-300 mx-1"></span>
            <button @click="section = 'all'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition" :class="section === 'all' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">All Sections</button>
            <button @click="section = 'peptides'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition" :class="section === 'peptides' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">Peptides ({{ $totalPeptides }})</button>
            <button @click="section = 'blog'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition" :class="section === 'blog' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">Blog ({{ $totalBlog }})</button>
            <button @click="section = 'pages'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition" :class="section === 'pages' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">Pages ({{ $totalPages }})</button>
            <button @click="section = 'static'" class="px-3 py-1.5 text-xs font-medium rounded-lg transition" :class="section === 'static' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">Static</button>
        </div>

        {{-- Static Pages (editable via Settings) --}}
        <div x-show="section === 'all' || section === 'static'" x-cloak>
            <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Static Pages
                <span class="text-xs font-normal text-gray-400">(stored in site settings)</span>
            </h3>
            <div class="space-y-2">
                @foreach($staticPages as $sp)
                    @include('admin.settings.partials.seo-review-row', [
                        'itemId' => $sp['key'],
                        'itemType' => 'static_page',
                        'itemName' => $sp['name'],
                        'itemSlug' => $sp['path'],
                        'metaTitle' => $sp['meta_title'],
                        'metaDescription' => $sp['meta_description'],
                        'editUrl' => route('admin.settings.seo'),
                        'canRegenerate' => false,
                    ])
                @endforeach
            </div>
        </div>

        {{-- Peptides --}}
        <div x-show="section === 'all' || section === 'peptides'" x-cloak>
            <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span> Peptides
                <span class="text-xs font-normal text-gray-400">({{ $peptidesSeo }}/{{ $totalPeptides }} have SEO)</span>
            </h3>
            <div class="space-y-2">
                @foreach($peptides as $item)
                    @include('admin.settings.partials.seo-review-row', [
                        'itemId' => $item->id,
                        'itemType' => 'peptide',
                        'itemName' => $item->name,
                        'itemSlug' => '/peptides/' . $item->slug,
                        'metaTitle' => $item->meta_title,
                        'metaDescription' => $item->meta_description,
                        'editUrl' => route('admin.peptides.edit', $item->slug),
                        'canRegenerate' => true,
                    ])
                @endforeach
            </div>
        </div>

        {{-- Blog Posts --}}
        <div x-show="section === 'all' || section === 'blog'" x-cloak>
            <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Blog Posts
                <span class="text-xs font-normal text-gray-400">({{ $blogSeo }}/{{ $totalBlog }} have SEO)</span>
            </h3>
            @if($blogPosts->isEmpty())
                <div class="card p-4 text-sm text-gray-500 text-center">No published blog posts yet.</div>
            @else
                <div class="space-y-2">
                    @foreach($blogPosts as $item)
                        @include('admin.settings.partials.seo-review-row', [
                            'itemId' => $item->id,
                            'itemType' => 'blog_post',
                            'itemName' => $item->title,
                            'itemSlug' => '/blog/' . $item->slug,
                            'metaTitle' => $item->meta_title,
                            'metaDescription' => $item->meta_description,
                            'editUrl' => route('admin.blog-posts.edit', $item->id),
                            'canRegenerate' => false,
                        ])
                    @endforeach
                </div>
            @endif
        </div>

        {{-- CMS Pages --}}
        <div x-show="section === 'all' || section === 'pages'" x-cloak>
            <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-orange-500"></span> CMS Pages
                <span class="text-xs font-normal text-gray-400">({{ $pagesSeo }}/{{ $totalPages }} have SEO)</span>
            </h3>
            @if($pages->isEmpty())
                <div class="card p-4 text-sm text-gray-500 text-center">No published pages.</div>
            @else
                <div class="space-y-2">
                    @foreach($pages as $item)
                        @include('admin.settings.partials.seo-review-row', [
                            'itemId' => $item->id,
                            'itemType' => 'page',
                            'itemName' => $item->title,
                            'itemSlug' => '/' . $item->slug,
                            'metaTitle' => $item->meta_title,
                            'metaDescription' => $item->meta_description,
                            'editUrl' => route('admin.pages.edit', $item->id),
                            'canRegenerate' => false,
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('seo', { filter: 'all' });
    });

    function seoReview() {
        return {
            filter: 'all',
            section: 'all',
            get titleIssues() {
                return document.querySelectorAll('[data-title-issue]').length;
            },
            get descIssues() {
                return document.querySelectorAll('[data-desc-issue]').length;
            },
            setFilter(f) {
                this.filter = f;
                Alpine.store('seo').filter = f;
            }
        };
    }

    function seoRow(id, type, title, desc) {
        return {
            id, type,
            title: title || '',
            desc: desc || '',
            origTitle: title || '',
            origDesc: desc || '',
            editing: false,
            saving: false,
            regenerating: false,
            message: '',
            messageSuccess: false,

            get hasTitle() { return this.title.length > 0; },
            get hasDesc() { return this.desc.length > 0; },
            get hasSeo() { return this.hasTitle && this.hasDesc; },
            get titleOk() { return this.title.length > 0 && this.title.length <= 60; },
            get descOk() { return this.desc.length > 0 && this.desc.length <= 155; },
            get isGood() { return this.titleOk && this.descOk; },
            get isMissing() { return !this.hasTitle || !this.hasDesc; },
            get hasIssues() { return (this.hasTitle && this.title.length > 60) || (this.hasDesc && this.desc.length > 155); },

            get visible() {
                const f = Alpine.store('seo')?.filter ?? 'all';
                if (f === 'all') return true;
                if (f === 'missing') return this.isMissing;
                if (f === 'issues') return this.hasIssues;
                if (f === 'good') return this.isGood;
                return true;
            },

            async save() {
                this.saving = true;
                try {
                    const res = await fetch('{{ route("admin.settings.seo.update-seo") }}', {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ type: this.type, id: this.id, meta_title: this.title, meta_description: this.desc }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.origTitle = this.title;
                        this.origDesc = this.desc;
                        this.editing = false;
                        this.flash('Saved!', true);
                    }
                } catch (e) { this.flash('Save failed', false); }
                this.saving = false;
            },

            cancel() {
                this.title = this.origTitle;
                this.desc = this.origDesc;
                this.editing = false;
            },

            async regenerate() {
                this.regenerating = true;
                this.message = '';
                try {
                    const res = await fetch('{{ route("admin.settings.seo.generate-one") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ peptide_id: this.id }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.title = data.meta_title;
                        this.desc = data.meta_description;
                        this.origTitle = data.meta_title;
                        this.origDesc = data.meta_description;
                        this.flash('Regenerated!', true);
                    } else { this.flash(data.error || 'Failed', false); }
                } catch (e) { this.flash('Network error', false); }
                this.regenerating = false;
            },

            flash(msg, success) {
                this.message = msg;
                this.messageSuccess = success;
                setTimeout(() => this.message = '', 3000);
            }
        };
    }
    </script>
    @endpush
</x-admin-layout>
