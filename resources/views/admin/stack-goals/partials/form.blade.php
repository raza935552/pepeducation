<form action="{{ $goal ? route('admin.stack-goals.update', $goal) : route('admin.stack-goals.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($goal) @method('PUT') @endif

    @if($errors->any())
        <div class="rounded-lg bg-red-50 p-4">
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Goal Details</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name', $goal?->name) }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $goal?->slug) }}" placeholder="Auto-generated from name"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('description', $goal?->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon (SVG or icon class)</label>
                        <textarea name="icon" rows="2" placeholder="Paste SVG code or icon class name"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold font-mono text-sm">{{ old('icon', $goal?->icon) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Image</h3>
                @if($goal?->image)
                    <div class="mb-4">
                        <img src="{{ Storage::url($goal->image) }}" alt="{{ $goal->name }}" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-gold/10 file:text-brand-gold hover:file:bg-brand-gold/20">
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="color" value="{{ old('color', $goal?->color ?? '#9A7B4F') }}"
                                class="h-10 w-14 rounded border-gray-300">
                            <input type="text" value="{{ old('color', $goal?->color ?? '#9A7B4F') }}" readonly
                                class="flex-1 rounded-lg border-gray-300 bg-gray-50 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input type="number" name="order" value="{{ old('order', $goal?->order ?? 0) }}" min="0"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $goal?->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span>Active</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full btn btn-primary">{{ $goal ? 'Update Goal' : 'Create Goal' }}</button>

            @if($goal)
                <form action="{{ route('admin.stack-goals.destroy', $goal) }}" method="POST"
                    onsubmit="return confirm('Delete this goal? Associated product links will be removed.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600">Delete Goal</button>
                </form>
            @endif
        </div>
    </div>
</form>
