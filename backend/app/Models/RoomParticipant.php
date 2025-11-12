<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $room_id
 * @property string $name
 * @property string $role
 * @property string $session_id
 * @property bool $is_online
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read Room $room
 * @property-read \Illuminate\Database\Eloquent\Collection|Vote[] $votes
 */
class RoomParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'role',
        'is_online',
        'last_seen_at',
    ];

    protected $hidden = [
        'session_id',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    protected $attributes = [
        'role' => 'participant',
        'is_online' => true,
    ];

    /**
     * Boot method - Auto-generate session ID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($participant) {
            if (empty($participant->session_id)) {
                $participant->session_id = Str::random(32);
            }
        });
    }

    /**
     * Get the room this participant belongs to
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get all votes by this participant
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'participant_id');
    }

    /**
     * Check if this participant is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if this participant is observer
     */
    public function isObserver(): bool
    {
        return $this->role === 'observer';
    }

    /**
     * Check if this participant can vote (not observer)
     */
    public function canVote(): bool
    {
        return in_array($this->role, ['participant', 'admin']);
    }

    /**
     * Verify session ID
     */
    public function verifySession(string $sessionId): bool
    {
        return hash_equals($this->session_id, $sessionId);
    }

    /**
     * Update last seen and mark online
     */
    public function ping(): self
    {
        $this->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark participant as offline
     */
    public function markOffline(): self
    {
        $this->update([
            'is_online' => false,
            'last_seen_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark participant as online
     */
    public function markOnline(): self
    {
        $this->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        return $this;
    }

    /**
     * Get vote for a specific story
     */
    public function getVoteForStory(int $storyId): ?Vote
    {
        return $this->votes()->where('story_id', $storyId)->first();
    }

    /**
     * Check if participant has voted for a story
     */
    public function hasVotedForStory(int $storyId): bool
    {
        return $this->votes()->where('story_id', $storyId)->exists();
    }

    /**
     * Scope: Only online participants
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    /**
     * Scope: Only offline participants
     */
    public function scopeOffline($query)
    {
        return $query->where('is_online', false);
    }

    /**
     * Scope: Only voters (no observers)
     */
    public function scopeVoters($query)
    {
        return $query->whereIn('role', ['participant', 'admin']);
    }

    /**
     * Scope: Only observers
     */
    public function scopeObservers($query)
    {
        return $query->where('role', 'observer');
    }

    /**
     * Scope: Only admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope: Find by session ID
     */
    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}
