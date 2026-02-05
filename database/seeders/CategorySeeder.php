<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Weight Loss', 'color' => '#ef4444'],
            ['name' => 'Diabetes', 'color' => '#f97316'],
            ['name' => 'Metabolism', 'color' => '#eab308'],
            ['name' => 'Heart Health', 'color' => '#ec4899'],
            ['name' => 'Gastrointestinal', 'color' => '#8b5cf6'],
            ['name' => 'Wound Healing', 'color' => '#06b6d4'],
            ['name' => 'Neurological Support', 'color' => '#3b82f6'],
            ['name' => 'Tissue Repair', 'color' => '#10b981'],
            ['name' => 'Anti-Aging', 'color' => '#f59e0b'],
            ['name' => 'Skin & Beauty', 'color' => '#ec4899'],
            ['name' => 'Athletic Recovery', 'color' => '#22c55e'],
            ['name' => 'Neuroprotection', 'color' => '#6366f1'],
            ['name' => 'Cognitive Enhancement', 'color' => '#8b5cf6'],
            ['name' => 'Anxiety Relief', 'color' => '#14b8a6'],
            ['name' => 'Fat Loss', 'color' => '#f43f5e'],
            ['name' => 'Joint Health', 'color' => '#0ea5e9'],
            ['name' => 'Hair Growth', 'color' => '#a855f7'],
            ['name' => 'Cellular Health', 'color' => '#84cc16'],
            ['name' => 'Energy & Metabolism', 'color' => '#fb923c'],
            ['name' => 'Longevity', 'color' => '#fbbf24'],
        ];

        foreach ($categories as $i => $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['color' => $category['color'], 'sort_order' => $i]
            );
        }
    }
}
