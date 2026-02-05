<?php

namespace Database\Seeders;

use App\Models\OutboundLink;
use Illuminate\Database\Seeder;

class ProductLinksSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            [
                'name' => 'Semaglutide - Fat Loss',
                'slug' => 'product-fat-loss',
                'destination_url' => 'https://fastpeptix.com/product/semaglutide',
                'utm_source' => 'pepprofesor',
                'utm_medium' => 'quiz',
                'utm_campaign' => 'product_match',
                'utm_content' => 'fat_loss',
                'append_segment' => true,
                'append_session' => true,
                'append_email' => true,
                'track_klaviyo' => true,
                'is_active' => true,
            ],
            [
                'name' => 'BPC-157 - Recovery',
                'slug' => 'product-recovery',
                'destination_url' => 'https://fastpeptix.com/product/bpc-157',
                'utm_source' => 'pepprofesor',
                'utm_medium' => 'quiz',
                'utm_campaign' => 'product_match',
                'utm_content' => 'recovery',
                'append_segment' => true,
                'append_session' => true,
                'append_email' => true,
                'track_klaviyo' => true,
                'is_active' => true,
            ],
            [
                'name' => 'CJC-1295 + Ipamorelin - Muscle',
                'slug' => 'product-muscle',
                'destination_url' => 'https://fastpeptix.com/product/cjc-1295-ipamorelin',
                'utm_source' => 'pepprofesor',
                'utm_medium' => 'quiz',
                'utm_campaign' => 'product_match',
                'utm_content' => 'muscle',
                'append_segment' => true,
                'append_session' => true,
                'append_email' => true,
                'track_klaviyo' => true,
                'is_active' => true,
            ],
            [
                'name' => 'GHK-Cu - Anti-Aging',
                'slug' => 'product-anti-aging',
                'destination_url' => 'https://fastpeptix.com/product/ghk-cu',
                'utm_source' => 'pepprofesor',
                'utm_medium' => 'quiz',
                'utm_campaign' => 'product_match',
                'utm_content' => 'anti_aging',
                'append_segment' => true,
                'append_session' => true,
                'append_email' => true,
                'track_klaviyo' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Semax - Cognitive',
                'slug' => 'product-cognitive',
                'destination_url' => 'https://fastpeptix.com/product/semax',
                'utm_source' => 'pepprofesor',
                'utm_medium' => 'quiz',
                'utm_campaign' => 'product_match',
                'utm_content' => 'cognitive',
                'append_segment' => true,
                'append_session' => true,
                'append_email' => true,
                'track_klaviyo' => true,
                'is_active' => true,
            ],
            [
                'name' => 'DSIP - Sleep',
                'slug' => 'product-sleep',
                'destination_url' => 'https://fastpeptix.com/product/dsip',
                'utm_source' => 'pepprofesor',
                'utm_medium' => 'quiz',
                'utm_campaign' => 'product_match',
                'utm_content' => 'sleep',
                'append_segment' => true,
                'append_session' => true,
                'append_email' => true,
                'track_klaviyo' => true,
                'is_active' => true,
            ],
        ];

        foreach ($links as $link) {
            OutboundLink::updateOrCreate(
                ['slug' => $link['slug']],
                $link
            );
        }
    }
}
