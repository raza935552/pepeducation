<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.results-bank.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span>{{ $result ? 'Edit Result Entry' : 'New Result Entry' }}</span>
        </div>
    </x-slot>

    <form action="{{ $result ? route('admin.results-bank.update', $result) : route('admin.results-bank.store') }}"
          method="POST">
        @csrf
        @if($result) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Classification --}}
                <div class="card p-6">
                    <h3 class="text-lg font-semibold mb-4">Classification</h3>
                    <p class="text-sm text-gray-500 mb-4">Each entry maps a health goal + experience level to a specific peptide recommendation. The combination must be unique.</p>

                    @php
                        $presetGoalKeys = json_encode(array_keys($healthGoals));
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{
                        goalMode: '{{ old('health_goal_custom_key') ? 'custom' : 'preset' }}',
                        selectedGoal: @js(old('health_goal', $result?->health_goal ?? ($prefillGoal ?? ''))),
                        customKey: @js(old('health_goal_custom_key', '')),
                        customLabel: @js(old('health_goal_custom_label', '')),
                        slugManuallyEdited: {{ old('health_goal_custom_key') ? 'true' : 'false' }},
                        presetKeys: {{ $presetGoalKeys }},
                        slugify(str) {
                            return str.toLowerCase().trim()
                                .replace(/[^a-z0-9\s_-]/g, '')
                                .replace(/[\s-]+/g, '_')
                                .replace(/_+/g, '_')
                                .replace(/^_|_$/g, '');
                        },
                        onLabelInput() {
                            if (!this.slugManuallyEdited) {
                                this.customKey = this.slugify(this.customLabel);
                            }
                        },
                        onKeyInput(val) {
                            this.slugManuallyEdited = true;
                            this.customKey = this.slugify(val);
                        },
                        get isConflict() {
                            return this.customKey && this.presetKeys.includes(this.customKey);
                        },
                    }">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Health Goal</label>
                            <select x-model="selectedGoal" x-show="goalMode === 'preset'"
                                    :name="goalMode === 'preset' ? 'health_goal' : ''"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">Select goal...</option>
                                @foreach($healthGoals as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>

                            <template x-if="goalMode === 'custom'">
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-[11px] font-medium text-gray-600 mb-0.5">Display Label</label>
                                        <input type="text" name="health_goal_custom_label" x-model="customLabel"
                                               @input="onLabelInput()"
                                               placeholder="e.g. Hair Growth & Restoration" required
                                               class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm">
                                        <p class="text-[10px] text-gray-400 mt-0.5">The friendly name shown in dropdowns and on the quiz.</p>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-medium text-gray-600 mb-0.5">Slug Key</label>
                                        <input type="text" name="health_goal_custom_key" x-model="customKey"
                                               @input="onKeyInput($event.target.value)"
                                               placeholder="auto-generated from label"
                                               class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm font-mono">
                                        <p class="text-[10px] text-gray-400 mt-0.5">Auto-generated from the label. Edit only if you need a different internal key.</p>
                                        <template x-if="isConflict">
                                            <p class="text-[11px] text-amber-600 mt-1 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                This key already exists in the preset list. Select it from the dropdown instead.
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="
                                    if (goalMode === 'custom') { customKey = ''; customLabel = ''; slugManuallyEdited = false; }
                                    goalMode = goalMode === 'preset' ? 'custom' : 'preset';
                                "
                                    class="mt-1.5 text-xs text-brand-gold hover:underline">
                                <span x-text="goalMode === 'preset' ? '+ Add custom goal' : '← Back to list'"></span>
                            </button>
                            @error('health_goal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            @error('health_goal_custom_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Experience Level</label>
                            @php
                                $currentLevels = $result
                                    ? old('experience_levels', [$result->experience_level])
                                    : old('experience_levels', []);
                            @endphp
                            <div class="space-y-2 mt-1">
                                @foreach($experienceLevels as $value => $label)
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 hover:border-brand-gold cursor-pointer transition-colors">
                                        <input type="checkbox" name="experience_levels[]" value="{{ $value }}"
                                               {{ in_array($value, $currentLevels) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                                        <span class="text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Select multiple to {{ $result ? 'also create entries for other levels' : 'create one entry per level' }}.</p>
                            @error('experience_levels') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            @error('experience_levels.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Peptide Info --}}
                <div class="card p-6" x-data="productAutoFill()">
                    <h3 class="text-lg font-semibold mb-4">Peptide Recommendation</h3>

                    <div class="space-y-4">
                        {{-- Linked Stack Product (TOP) --}}
                        <div>
                            <label for="stack_product_id" class="block text-sm font-medium text-gray-700 mb-1">Linked Stack Product</label>
                            <select name="stack_product_id" id="stack_product_id" x-model="selectedProductId"
                                    @change="onProductChange()"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- No linked product --</option>
                                @foreach($stackProducts as $product)
                                    <option value="{{ $product->id }}" {{ old('stack_product_id', $result?->stack_product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->slug }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Select a product to auto-fill the fields below. You can still override any value.</p>
                            @error('stack_product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Auto-fill notice --}}
                        <div x-show="justFilled" x-transition.opacity class="rounded-lg bg-green-50 border border-green-200 px-3 py-2">
                            <p class="text-xs text-green-700 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Fields auto-filled from product. Edit any field to customize.
                            </p>
                        </div>

                        <div>
                            <label for="peptide_name" class="block text-sm font-medium text-gray-700 mb-1">Peptide Name</label>
                            <input type="text" name="peptide_name" id="peptide_name" x-model="peptideName"
                                   placeholder="e.g. Tirzepatide, BPC-157"
                                   class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold" required>
                            @error('peptide_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="peptide_slug" class="block text-sm font-medium text-gray-700 mb-1">Peptide Slug <span class="font-normal text-gray-400">(optional)</span></label>
                            <input type="text" name="peptide_slug" id="peptide_slug" x-model="peptideSlug"
                                   placeholder="e.g. tirzepatide"
                                   class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <p class="text-xs text-gray-500 mt-1">URL-friendly name. Used to link to the peptide detail page if it exists.</p>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="font-normal text-gray-400">(optional)</span></label>
                            <textarea name="description" id="description" rows="3" x-model="description"
                                      placeholder="Short description of why this peptide is recommended for this goal..."
                                      class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"></textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="benefits_text" class="block text-sm font-medium text-gray-700 mb-1">Key Benefits <span class="font-normal text-gray-400">(one per line)</span></label>
                            <textarea name="benefits_text" id="benefits_text" rows="4" x-model="benefitsText"
                                      placeholder="Promotes fat oxidation&#10;Reduces appetite naturally&#10;Improves insulin sensitivity"
                                      class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Each line becomes a benefit bullet point on the reveal slide.</p>
                        </div>
                    </div>
                </div>

                @php
                    $stackProductsJson = $stackProducts->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'slug' => $p->slug,
                        'description' => $p->description,
                        'key_benefits' => $p->key_benefits ?? [],
                    ])->keyBy('id')->toJson();
                @endphp
                <script>
                function productAutoFill() {
                    return {
                        products: {!! $stackProductsJson !!},
                        selectedProductId: '{{ old('stack_product_id', $result?->stack_product_id ?? '') }}',
                        peptideName: @js(old('peptide_name', $result?->peptide_name ?? '')),
                        peptideSlug: @js(old('peptide_slug', $result?->peptide_slug ?? '')),
                        description: @js(old('description', $result?->description ?? '')),
                        benefitsText: @js(old('benefits_text', $result ? implode("\n", $result->benefits ?? []) : '')),
                        justFilled: false,
                        onProductChange() {
                            if (!this.selectedProductId) return;
                            const product = this.products[this.selectedProductId];
                            if (!product) return;

                            this.peptideName = product.name;
                            this.peptideSlug = product.slug;
                            if (product.description) this.description = product.description;
                            if (product.key_benefits && product.key_benefits.length > 0) {
                                this.benefitsText = product.key_benefits.join('\n');
                            }

                            this.justFilled = true;
                            setTimeout(() => this.justFilled = false, 4000);
                        },
                    };
                }
                </script>

                {{-- Rating & Testimonial --}}
                <div class="card p-6">
                    <h3 class="text-lg font-semibold mb-4">Rating & Social Proof</h3>
                    <p class="text-sm text-gray-500 mb-4">All fields are optional. Use the toggles to control what shows on the quiz reveal slide.</p>

                    <div class="space-y-4">
                        @php $displayFields = old('display_fields', $result?->display_fields ?? []); @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="star_rating" class="text-sm font-medium text-gray-700">Star Rating (1.0 - 5.0)</label>
                                    <label class="flex items-center gap-1 text-xs text-gray-500">
                                        <input type="hidden" name="display_fields[star_rating]" value="0">
                                        <input type="checkbox" name="display_fields[star_rating]" value="1"
                                               {{ ($displayFields['star_rating'] ?? true) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold h-3.5 w-3.5">
                                        Show
                                    </label>
                                </div>
                                <input type="number" name="star_rating" id="star_rating"
                                       value="{{ old('star_rating', $result?->star_rating ?? '4.8') }}"
                                       step="0.1" min="1" max="5"
                                       class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                @error('star_rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="rating_label" class="block text-sm font-medium text-gray-700 mb-1">Rating Label</label>
                                <input type="text" name="rating_label" id="rating_label"
                                       value="{{ old('rating_label', $result?->rating_label ?? 'Excellent Match') }}"
                                       placeholder="e.g. Excellent Match, Strong Match"
                                       class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                @error('rating_label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="testimonial" class="text-sm font-medium text-gray-700">Testimonial</label>
                                <label class="flex items-center gap-1 text-xs text-gray-500">
                                    <input type="hidden" name="display_fields[testimonial]" value="0">
                                    <input type="checkbox" name="display_fields[testimonial]" value="1"
                                           {{ ($displayFields['testimonial'] ?? true) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold h-3.5 w-3.5">
                                    Show
                                </label>
                            </div>
                            <textarea name="testimonial" id="testimonial" rows="3"
                                      placeholder="Real user quote about their experience with this peptide..."
                                      class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('testimonial', $result?->testimonial) }}</textarea>
                            @error('testimonial') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="testimonial_author" class="block text-sm font-medium text-gray-700 mb-1">Testimonial Author (optional)</label>
                            <input type="text" name="testimonial_author" id="testimonial_author"
                                   value="{{ old('testimonial_author', $result?->testimonial_author) }}"
                                   placeholder="e.g. Sarah M., Age 34"
                                   class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                </div>
            </div>

                {{-- Accordion Sections --}}
                <div class="card p-6" x-data="{
                    items: @js(old('accordion_items', $result?->accordion_items ?? [])),
                    addItem() { this.items.push({ title: '', content: '' }); },
                    removeItem(i) { this.items.splice(i, 1); },
                }">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">Accordion Sections</h3>
                            <p class="text-sm text-gray-500 mt-1">Collapsible info sections shown on the peptide reveal slide. Specific to this peptide.</p>
                        </div>
                        <button type="button" @click="addItem()" class="text-sm text-brand-gold hover:underline font-medium">+ Add Section</button>
                    </div>

                    <template x-if="items.length === 0">
                        <p class="text-sm text-gray-400 italic">No accordion sections yet. Click "Add Section" to create one.</p>
                    </template>

                    <div class="space-y-3">
                        <template x-for="(item, i) in items" :key="i">
                            <div class="border rounded-lg p-4 bg-gray-50/50">
                                <div class="flex items-start gap-2 mb-2">
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-600 mb-1" x-text="'Section ' + (i + 1) + ' Title'"></label>
                                        <input type="text" x-model="item.title"
                                            :name="'accordion_items[' + i + '][title]'"
                                            placeholder="e.g. How does it work?"
                                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm">
                                    </div>
                                    <button type="button" @click="removeItem(i)" class="mt-5 text-red-400 hover:text-red-600 p-1" title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Content</label>
                                    <textarea x-model="item.content"
                                        :name="'accordion_items[' + i + '][content]'"
                                        placeholder="Detailed content for this section..."
                                        rows="3"
                                        class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm"></textarea>
                                </div>
                            </div>
                        </template>
                    </div>

                    <template x-if="items.length > 0">
                        <div class="mt-3">
                            <button type="button" @click="addItem()" class="text-sm text-brand-gold hover:underline font-medium">+ Add Another Section</button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                <div class="card p-6">
                    <h3 class="text-lg font-semibold mb-4">Status</h3>

                    <label class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $result?->is_active ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-2">Inactive entries won't be shown in quiz results.</p>
                </div>

                @if($result)
                    <div class="card p-6">
                        <h3 class="text-lg font-semibold mb-2">Info</h3>
                        <dl class="text-sm space-y-2">
                            <div>
                                <dt class="text-gray-500">Created</dt>
                                <dd class="text-gray-900">{{ $result->created_at->format('M j, Y g:ia') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Updated</dt>
                                <dd class="text-gray-900">{{ $result->updated_at->format('M j, Y g:ia') }}</dd>
                            </div>
                        </dl>
                    </div>
                @endif

                <button type="submit" class="w-full btn btn-primary">
                    {{ $result ? 'Update Entry' : 'Create Entry' }}
                </button>
            </div>
        </div>
    </form>

    @if($result)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-0">
            <div class="lg:col-start-3">
                <form action="{{ route('admin.results-bank.destroy', $result) }}" method="POST"
                      onsubmit="return confirm('Delete this result entry?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600">Delete Entry</button>
                </form>
            </div>
        </div>
    @endif
</x-admin-layout>
