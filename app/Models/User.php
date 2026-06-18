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
        'slug',
        'email',
        'password',
        'role',
        'bio',
        'avatar',
        'credentials',
        'expertise',
        'twitter_url',
        'linkedin_url',
        'is_public_author',
        'is_suspended',
        'suspended_at',
        'is_seed',
        'last_seen_at',
        'forum_posts_count',
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
            'is_public_author' => 'boolean',
            'is_seed' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    public function authoredPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'created_by');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function expertiseList(): array
    {
        if (empty($this->expertise)) {
            return [];
        }

        return array_map('trim', explode(',', $this->expertise));
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
        return $this->preferences()->firstOrCreate([], [
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

    // ---- Community (forum) ----
    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }

    public function forumPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    public function forumSubscriptions(): HasMany
    {
        return $this->hasMany(ForumSubscription::class);
    }

    /**
     * Whether this user may participate in the community (verified, not suspended).
     */
    public function canParticipateInCommunity(): bool
    {
        return $this->hasVerifiedEmail() && ! $this->is_suspended;
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $letters = array_map(fn ($p) => mb_substr($p, 0, 1), array_slice($parts, 0, 2));

        return strtoupper(implode('', $letters)) ?: 'U';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (empty($this->avatar)) {
            return null;
        }

        return str_starts_with($this->avatar, 'http') || str_starts_with($this->avatar, '/')
            ? $this->avatar
            : '/storage/' . ltrim($this->avatar, '/');
    }
}
