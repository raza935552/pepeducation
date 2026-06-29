<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add the two remaining sellable Biolinx products to the peptide quiz so the
 * quiz catalogue matches the live Biolinx store:
 *   - Glow Blend (GHK-Cu + BPC-157 + TB-500)
 *   - Cagrilintide + Semaglutide blend (Biolinx "C-AMYLIN / G1-S")
 *
 * Each is created as a StackProduct + linked to the BIOLINX store (#15) with a
 * buy link, mirroring the existing 22 Biolinx-linked quiz products (see KLOW).
 * Idempotent: safe to re-run.
 */
return new class extends Migration
{
    private const BIOLINX_STORE_ID = 15;
    private const BIOLINX_OUTBOUND_LINK_ID = 1; // same tracking link the other Biolinx products use

    public function up(): void
    {
        $products = [
            [
                'name'      => 'Glow Blend (GHK-Cu + BPC-157 + TB-500)',
                'slug'      => 'glow-blend',
                'price'     => 149.93,
                'url'       => 'https://biolinxlabs.com/products/glow-gh-cu-50mg-bpc157-10mg-tb500-10mg-70mg',
                'goalSlugs' => ['look-younger', 'recovery'],
            ],
            [
                'name'      => 'Cagrilintide + Semaglutide Blend',
                'slug'      => 'cagrilintide-semaglutide-blend',
                'price'     => 129.93,
                'url'       => 'https://biolinxlabs.com/products/c-amylin-g1-s-blend-5-mg',
                'goalSlugs' => ['fat-loss'],
            ],
        ];

        foreach ($products as $p) {
            // 1) StackProduct (match the shape of the existing blends: active + has_deal)
            $productId = DB::table('stack_products')->where('slug', $p['slug'])->value('id');
            if (!$productId) {
                $productId = DB::table('stack_products')->insertGetId([
                    'name'       => $p['name'],
                    'slug'       => $p['slug'],
                    'price'      => 0.00, // display price comes from the store pivot, like KLOW
                    'is_active'  => 1,
                    'has_deal'   => 1,
                    'order'      => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('stack_products')->where('id', $productId)->update([
                    'name'      => $p['name'],
                    'is_active' => 1,
                    'has_deal'  => 1,
                    'updated_at' => now(),
                ]);
            }

            // 2) BIOLINX store link (the buy link the quiz reveals)
            $existing = DB::table('stack_store_product')
                ->where('stack_store_id', self::BIOLINX_STORE_ID)
                ->where('stack_product_id', $productId)
                ->first();

            $pivot = [
                'price'                => $p['price'],
                'url'                  => $p['url'],
                'outbound_link_id'     => self::BIOLINX_OUTBOUND_LINK_ID,
                'is_in_stock'          => 1,
                'availability_status'  => 'in_stock',
                'is_recommended'       => 1,
                'updated_at'           => now(),
            ];

            if ($existing) {
                DB::table('stack_store_product')->where('id', $existing->id)->update($pivot);
            } else {
                DB::table('stack_store_product')->insert(array_merge($pivot, [
                    'stack_store_id'   => self::BIOLINX_STORE_ID,
                    'stack_product_id' => $productId,
                    'created_at'       => now(),
                ]));
            }

            // 3) Goal associations (for goal-based browsing). Skip silently if a goal slug is absent.
            foreach ($p['goalSlugs'] as $goalSlug) {
                $goalId = DB::table('stack_goals')->where('slug', $goalSlug)->value('id');
                if (!$goalId) {
                    continue;
                }
                $hasLink = DB::table('goal_stack_product')
                    ->where('stack_goal_id', $goalId)
                    ->where('stack_product_id', $productId)
                    ->exists();
                if (!$hasLink) {
                    DB::table('goal_stack_product')->insert([
                        'stack_goal_id'    => $goalId,
                        'stack_product_id' => $productId,
                        'order'            => 0,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $slugs = ['glow-blend', 'cagrilintide-semaglutide-blend'];
        $ids = DB::table('stack_products')->whereIn('slug', $slugs)->pluck('id');

        DB::table('goal_stack_product')->whereIn('stack_product_id', $ids)->delete();
        DB::table('stack_store_product')->whereIn('stack_product_id', $ids)->delete();
        DB::table('stack_products')->whereIn('id', $ids)->delete();
    }
};
