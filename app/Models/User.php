<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bio',
        'avatar',
        'is_suspended',
        'suspended_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuspended(): bool
    {
        return $this->is_suspended;
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkedPeptides()
    {
        return $this->belongsToMany(Peptide::class, 'bookmarks')
            ->withTimestamps();
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    public function hasBookmarked(Peptide $peptide): bool
    {
        return $this->bookmarks()->where('peptide_id', $peptide->id)->exists();
    }

    public function getOrCreatePreferences(): UserPreference
    {
        return $this->preferences ?? $this->preferences()->create([
            'notify_edit_status' => true,
            'notify_marketing' => false,
            'notify_weekly_digest' => false,
            'data_usage_opt_in' => false,
        ]);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function peptideRequests(): HasMany
    {
        return $this->hasMany(PeptideRequest::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    // Marketing & Tracking Relationships
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(UserEvent::class);
    }

    public function quizResponses(): HasMany
    {
        return $this->hasMany(QuizResponse::class);
    }

    public function outboundClicks(): HasMany
    {
        return $this->hasMany(OutboundClick::class);
    }

    public function leadMagnetDownloads(): HasMany
    {
        return $this->hasMany(LeadMagnetDownload::class);
    }
}
