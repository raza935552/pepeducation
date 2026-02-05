<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class LeadMagnet extends Model
{
    protected $fillable = [
        'name', 'slug', 'description',
        'file_path', 'file_name', 'file_type', 'file_size',
        'thumbnail', 'preview_image',
        'segment', 'delivery_method', 'download_button_text',
        'landing_headline', 'landing_description', 'landing_benefits',
        'klaviyo_flow_id', 'klaviyo_event', 'klaviyo_property_name',
        'views_count', 'downloads_count',
        'is_active',
    ];

    protected $casts = [
        'landing_benefits' => 'array',
        'is_active' => 'boolean',
    ];

    // Delivery Methods
    public const DELIVERY_INSTANT = 'instant';
    public const DELIVERY_EMAIL = 'email';

    // File Types
    public const TYPE_PDF = 'pdf';
    public const TYPE_VIDEO = 'video';
    public const TYPE_ZIP = 'zip';

    // Relationships
    public function downloads(): HasMany
    {
        return $this->hasMany(LeadMagnetDownload::class);
    }

    public function popups(): HasMany
    {
        return $this->hasMany(Popup::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSegment($query, string $segment)
    {
        return $query->where('segment', $segment)
            ->orWhere('segment', 'all');
    }

    // Methods
    public function getDownloadUrl(): string
    {
        return route('lead-magnet.download', $this->slug);
    }

    public function getLandingUrl(): string
    {
        return route('lead-magnet.landing', $this->slug);
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor] ?? 'B');
    }

    public function getConversionRate(): float
    {
        if ($this->views_count === 0) return 0;
        return round($this->downloads_count / $this->views_count * 100, 2);
    }

    public function getFileUrl(): ?string
    {
        if (!$this->file_path) return null;
        return Storage::disk('public')->url($this->file_path);
    }
}
