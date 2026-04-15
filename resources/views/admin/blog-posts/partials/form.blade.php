@php
    $post = $blogPost ?? null;
    $isEdit = (bool) $post;
    $selectedCategories = old('categories', $post?->categories->pluck('id')->toArray() ?? []);
    $selectedTags = old('tags', $post?->tags->pluck('id')->toArray() ?? []);
    $selectedPeptides = old('peptides', $post?->peptides->pluck('id')->toArray() ?? []);
@endphp

<form action="{{ $isEdit ? route('admin.blog-posts.update', $post) : route('admin.blog-posts.store') }}"
      method="POST" class="space-y-6" x-data="blogForm()">
    @csrf
    @if($isEdit) @method('PUT') @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content Column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Title --}}
            <div class="card p-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title', $post?->title) }}" required
                        class="input w-full text-lg font-semibold" placeholder="Blog post title...">
                </div>
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $post?->slug) }}"
                        class="input w-full font-mono text-sm" placeholder="auto-generated-from-title">
                    <p class="text-xs text-gray-400 mt-1">Leave blank to auto-generate from title.</p>
                </div>
            </div>

            {{-- Excerpt --}}
            <div class="card p-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
                <textarea name="excerpt" rows="2" class="input w-full text-sm"
                    placeholder="Short summary for listing cards and search results...">{{ old('excerpt', $post?->excerpt) }}</textarea>
            </div>

            {{-- HTML Content --}}
            <div class="card p-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Content (HTML)</label>
                    <div class="flex gap-2">
                        <label class="text-xs px-3 py-1 rounded-lg border transition bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 cursor-pointer inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Upload Image
                            <input type="file" accept="image/*" @change="uploadImage($event)" class="hidden">
                        </label>
                        <button type="button" @click="previewMode = !previewMode"
                            class="text-xs px-3 py-1 rounded-lg border transition"
                            :class="previewMode ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-gray-50 text-gray-600 border-gray-200'">
                            <span x-text="previewMode ? 'Edit' : 'Preview'"></span>
                        </button>
                        @if($isEdit)
                        <button type="button" @click="generateContent()" :disabled="aiLoading"
                            class="text-xs px-3 py-1 rounded-lg border transition inline-flex items-center gap-1"
                            :class="aiLoading ? 'bg-gray-100 text-gray-400' : 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100'">
                            <svg x-show="!aiLoading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <svg x-show="aiLoading" class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span x-text="aiLoading ? 'Generating...' : 'Rewrite with AI'"></span>
                        </button>
                        @endif
                    </div>
                </div>
                <p x-show="aiMessage" x-cloak class="mb-2 text-xs p-2 rounded-lg"
                   :class="aiSuccess ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'" x-text="aiMessage"></p>

                <div x-show="!previewMode">
                    <textarea name="html" rows="24" x-ref="htmlEditor"
                        class="input w-full font-mono text-sm leading-relaxed"
                        placeholder="<h2>Section Heading</h2>&#10;<p>Paragraph content...</p>">{{ old('html', $post?->html) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Use h2, h3, p, ul/li, ol/li, strong, em tags. No h1 needed (title is separate).</p>
                </div>
                <div x-show="previewMode" x-cloak
                     class="prose prose-sm max-w-none border rounded-lg p-4 bg-gray-50 min-h-[400px]"
                     x-html="$refs.htmlEditor.value">
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Publish Settings --}}
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Publish</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="input w-full">
                            @foreach(['draft' => 'Draft', 'published' => 'Published', 'scheduled' => 'Scheduled'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $post?->status ?? 'draft') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Publish Date</label>
                        <input type="datetime-local" name="published_at"
                            value="{{ old('published_at', $post?->published_at?->format('Y-m-d\TH:i')) }}"
                            class="input w-full text-sm">
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1"
                            {{ old('is_featured', $post?->is_featured) ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-admin-primary-600 focus:ring-admin-primary-500">
                        <span class="text-gray-700">Featured Post</span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Featured Image URL</label>
                        <input type="text" name="featured_image" value="{{ old('featured_image', $post?->featured_image) }}"
                            class="input w-full text-sm" placeholder="/storage/blog/image.jpg">
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1">{{ $isEdit ? 'Update Post' : 'Create Post' }}</button>
                    @if($isEdit)
                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-secondary text-sm">View</a>
                    @endif
                </div>
            </div>

            {{-- Categories --}}
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-3 cursor-pointer p-1.5 rounded hover:bg-gray-50">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                {{ in_array($cat->id, $selectedCategories) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-gray-300 text-admin-primary-600">
                            <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Tags --}}
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-3 cursor-pointer p-1.5 rounded hover:bg-gray-50">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-gray-300 text-admin-primary-600">
                            <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-3">
                    <input type="text" name="new_tags" placeholder="Add new tags (comma-separated)"
                        class="input w-full text-sm">
                </div>
            </div>

            {{-- Related Peptides --}}
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Peptides</h3>
                <div class="space-y-1 max-h-48 overflow-y-auto">
                    @foreach($peptides as $pep)
                        <label class="flex items-center gap-3 cursor-pointer p-1.5 rounded hover:bg-gray-50">
                            <input type="checkbox" name="peptides[]" value="{{ $pep->id }}"
                                {{ in_array($pep->id, $selectedPeptides) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-gray-300 text-admin-primary-600">
                            <span class="text-sm text-gray-700">{{ $pep->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- SEO --}}
            <div class="card p-6" x-data="{ titleLen: '{{ strlen(old('meta_title', $post?->meta_title ?? '')) }}', descLen: '{{ strlen(old('meta_description', $post?->meta_description ?? '')) }}' }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">SEO</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                            <span class="text-xs" :class="titleLen > 60 ? 'text-red-500' : 'text-gray-400'" x-text="titleLen + '/60'"></span>
                        </div>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $post?->meta_title) }}"
                            class="input w-full text-sm" placeholder="SEO title..."
                            @input="titleLen = $event.target.value.length">
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                            <span class="text-xs" :class="descLen > 155 ? 'text-red-500' : 'text-gray-400'" x-text="descLen + '/155'"></span>
                        </div>
                        <textarea name="meta_description" rows="3" class="input w-full text-sm"
                            placeholder="SEO description..."
                            @input="descLen = $event.target.value.length">{{ old('meta_description', $post?->meta_description) }}</textarea>
                    </div>
                    {{-- Google Preview --}}
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs text-gray-400 mb-1">Google Preview</p>
                        <p class="text-blue-700 text-sm font-medium truncate">{{ $post?->meta_title ?? $post?->title ?? 'Post Title' }}</p>
                        <p class="text-green-700 text-xs">professorpeptides.co/blog/{{ $post?->slug ?? '...' }}</p>
                        <p class="text-gray-600 text-xs mt-0.5 line-clamp-2">{{ $post?->meta_description ?? 'Enter a meta description...' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function blogForm() {
    return {
        previewMode: false,
        aiLoading: false,
        aiMessage: '',
        aiSuccess: false,

        async uploadImage(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);

            try {
                const res = await fetch('{{ route("admin.blog-posts.upload-image") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });
                const data = await res.json();
                if (data.data && data.data[0]) {
                    const imgTag = '\n<img src="' + data.data[0] + '" alt="' + file.name.replace(/\.[^.]+$/, '') + '">\n';
                    const editor = this.$refs.htmlEditor;
                    const pos = editor.selectionStart;
                    editor.value = editor.value.substring(0, pos) + imgTag + editor.value.substring(pos);
                    editor.focus();
                    this.aiMessage = 'Image uploaded and inserted!';
                    this.aiSuccess = true;
                } else {
                    this.aiMessage = data.errors?.image?.[0] || 'Upload failed.';
                    this.aiSuccess = false;
                }
            } catch (e) {
                this.aiMessage = 'Upload failed: ' + e.message;
                this.aiSuccess = false;
            }
            event.target.value = '';
            setTimeout(() => this.aiMessage = '', 4000);
        },

        async generateContent() {
            const title = document.querySelector('input[name="title"]').value;
            if (!title) { this.aiMessage = 'Enter a title first.'; this.aiSuccess = false; return; }

            this.aiLoading = true;
            this.aiMessage = '';

            try {
                const res = await fetch('{{ route("admin.settings.seo.rewrite-overview") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ peptide_id: {{ $post?->peptides->first()?->id ?? 0 }} }),
                });
                const data = await res.json();
                if (data.success && data.overview) {
                    // Wrap plain text paragraphs in <p> tags
                    const html = data.overview.split('\n\n').map(p => '<p>' + p.trim() + '</p>').join('\n');
                    this.$refs.htmlEditor.value = html;
                    this.aiMessage = 'Content generated! Review and edit before saving.';
                    this.aiSuccess = true;
                } else {
                    this.aiMessage = data.error || 'AI generation failed.';
                    this.aiSuccess = false;
                }
            } catch (e) {
                this.aiMessage = 'Network error.';
                this.aiSuccess = false;
            }
            this.aiLoading = false;
            setTimeout(() => this.aiMessage = '', 5000);
        }
    };
}
</script>
@endpush
