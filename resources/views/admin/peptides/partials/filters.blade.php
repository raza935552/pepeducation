<div class="card mb-6">
    <form method="GET" action="{{ route('admin.peptides.index') }}" class="flex flex-wrap gap-4">
        <!-- Search -->
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search peptides..."
                   class="input w-full">
        </div>

        <!-- Status Filter -->
        <div class="w-40">
            <select name="status" class="input w-full">
                <option value="">All Status</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
        </div>

        <!-- Research Status Filter -->
        <div class="w-48">
            <select name="research" class="input w-full">
                <option value="">All Research</option>
                <option value="extensive" {{ request('research') === 'extensive' ? 'selected' : '' }}>Extensive</option>
                <option value="well" {{ request('research') === 'well' ? 'selected' : '' }}>Well Researched</option>
                <option value="emerging" {{ request('research') === 'emerging' ? 'selected' : '' }}>Emerging</option>
                <option value="limited" {{ request('research') === 'limited' ? 'selected' : '' }}>Limited</option>
            </select>
        </div>

        <!-- Actions -->
        <div class="flex gap-2">
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="{{ route('admin.peptides.index') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>
</div>
