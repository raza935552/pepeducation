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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="health_goal" class="block text-sm font-medium text-gray-700 mb-1">Health Goal</label>
                            <select name="health_goal" id="health_goal"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold" required>
                                <option value="">Select goal...</option>
                                @foreach($healthGoals as $value => $label)
                                    <option value="{{ $value }}" {{ old('health_goal', $result?->health_goal) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('health_goal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="experience_level" class="block text-sm font-medium text-gray-700 mb-1">Experience Level</label>
                            <select name="experience_level" id="experience_level"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold" required>
                                <option value="">Select level...</option>
                                @foreach($experienceLevels as $value => $label)
                                    <option value="{{ $value }}" {{ old('experience_level', $result?->experience_level) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('experience_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Peptide Info --}}
                <div class="card p-6">
                    <h3 class="text-lg font-semibold mb-4">Peptide Recommendation</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="peptide_name" class="block text-sm font-medium text-gray-700 mb-1">Peptide Name</label>
                            <input type="text" name="peptide_name" id="peptide_name"
                                   value="{{ old('peptide_name', $result?->peptide_name) }}"
                                   placeholder="e.g. Tirzepatide, BPC-157"
                                   class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold" required>
                            @error('peptide_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="peptide_slug" class="block text-sm font-medium text-gray-700 mb-1">Peptide Slug (optional)</label>
                            <input type="text" name="peptide_slug" id="peptide_slug"
                                   value="{{ old('peptide_slug', $result?->peptide_slug) }}"
                                   placeholder="e.g. tirzepatide"
                                   class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <p class="text-xs text-gray-500 mt-1">URL-friendly name. Used to link to the peptide detail page if it exists.</p>
                        </div>

                        <div>
                            <label for="stack_product_id" class="block text-sm font-medium text-gray-700 mb-1">Linked Stack Product</label>
                            <select name="stack_product_id" id="stack_product_id"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- No linked product --</option>
                                @foreach($stackProducts as $product)
                                    <option value="{{ $product->id }}" {{ old('stack_product_id', $result?->stack_product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->slug }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Links this recommendation to a Stack Builder product for vendor comparison on the quiz.</p>
                            @error('stack_product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                            <textarea name="description" id="description" rows="3"
                                      placeholder="Short description of why this peptide is recommended for this goal..."
                                      class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('description', $result?->description) }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="benefits_text" class="block text-sm font-medium text-gray-700 mb-1">Key Benefits (one per line)</label>
                            <textarea name="benefits_text" id="benefits_text" rows="4"
                                      placeholder="Promotes fat oxidation&#10;Reduces appetite naturally&#10;Improves insulin sensitivity"
                                      class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('benefits_text', $result ? implode("\n", $result->benefits ?? []) : '') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Each line becomes a benefit bullet point on the reveal slide.</p>
                        </div>
                    </div>
                </div>

                {{-- Rating & Testimonial --}}
                <div class="card p-6">
                    <h3 class="text-lg font-semibold mb-4">Rating & Social Proof</h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="star_rating" class="block text-sm font-medium text-gray-700 mb-1">Star Rating (1.0 - 5.0)</label>
                                <input type="number" name="star_rating" id="star_rating"
                                       value="{{ old('star_rating', $result?->star_rating ?? '4.8') }}"
                                       step="0.1" min="1" max="5"
                                       class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold" required>
                                @error('star_rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="rating_label" class="block text-sm font-medium text-gray-700 mb-1">Rating Label</label>
                                <input type="text" name="rating_label" id="rating_label"
                                       value="{{ old('rating_label', $result?->rating_label ?? 'Excellent Match') }}"
                                       placeholder="e.g. Excellent Match, Strong Match"
                                       class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold" required>
                                @error('rating_label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="testimonial" class="block text-sm font-medium text-gray-700 mb-1">Testimonial</label>
                            <textarea name="testimonial" id="testimonial" rows="3"
                                      placeholder="Real user quote about their experience with this peptide..."
                                      class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold" required>{{ old('testimonial', $result?->testimonial) }}</textarea>
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
