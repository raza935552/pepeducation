<?php

namespace Database\Seeders;

use App\Models\LeadMagnet;
use Illuminate\Database\Seeder;

class LeadMagnetSeeder extends Seeder
{
    public function run(): void
    {
        LeadMagnet::create([
            'name' => 'Peptide Beginner Guide',
            'slug' => 'peptide-guide',
            'description' => 'A comprehensive guide to peptides for beginners.',
            'file_path' => 'lead-magnets/peptide-guide.pdf',
            'file_name' => 'Peptide-Beginner-Guide.pdf',
            'file_type' => 'pdf',
            'segment' => 'all',
            'delivery_method' => LeadMagnet::DELIVERY_INSTANT,
            'download_button_text' => 'Download Free Guide',
            'landing_headline' => 'Master Peptides: The Complete Beginner Guide',
            'landing_description' => 'Learn everything you need to know about peptides in this free comprehensive guide.',
            'landing_benefits' => [
                'Understand peptide basics and terminology',
                'Learn safe reconstitution techniques',
                'Dosage calculation made simple',
                'Storage and handling best practices',
            ],
            'is_active' => true,
        ]);
    }
}
