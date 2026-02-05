<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Peptide extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'full_name',
        'abbreviation',
        'type',
        'typical_dose',
        'dose_frequency',
        'route',
        'injection_sites',
        'cycle',
        'storage',
        'research_status',
        'is_published',
        'overview',
        'key_benefits',
        'mechanism_of_action',
        'what_to_expect',
        'safety_warnings',
        'molecular_weight',
        'amino_acid_length',
        'amino_acid_sequence',
        'molecular_notes',
        'peak_time',
        'half_life',
        'clearance_time',
        'protocols',
        'compatible_peptides',
        'reconstitution_steps',
        'quality_indicators',
        'effectiveness_ratings',
        'references',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'injection_sites' => 'array',
        'key_benefits' => 'array',
        'what_to_expect' => 'array',
        'safety_warnings' => 'array',
        'protocols' => 'array',
        'compatible_peptides' => 'array',
        'reconstitution_steps' => 'array',
        'quality_indicators' => 'array',
        'effectiveness_ratings' => 'array',
        'references' => 'array',
        'is_published' => 'boolean',
        'molecular_weight' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($peptide) {
            if (empty($peptide->slug)) {
                $peptide->slug = Str::slug($peptide->name);
            }
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getResearchStatusBadgeAttribute(): array
    {
        return match($this->research_status) {
            'extensive' => ['label' => 'Extensively Studied', 'color' => 'blue'],
            'well' => ['label' => 'Well Researched', 'color' => 'green'],
            'emerging' => ['label' => 'Emerging Research', 'color' => 'yellow'],
            'limited' => ['label' => 'Limited Research', 'color' => 'gray'],
            default => ['label' => 'Unknown', 'color' => 'gray'],
        };
    }
}
