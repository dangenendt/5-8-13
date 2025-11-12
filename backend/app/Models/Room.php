<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $card_deck
 * @property bool $allow_observers
 * @property int|null $voting_time_limit
 * @property string|null $admin_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Room extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'card_deck',
        'allow_observers',
        'voting_time_limit',
    ];

    protected $hidden = [
        'admin_token',
    ];

    protected $casts = [
        'allow_observers' => 'boolean',
        'voting_time_limit' => 'integer',
    ];

    /**
     * Boot method - Auto-generate slug and admin token
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            if (empty($room->slug)) {
                $room->slug = Str::slug($room->name) . '-' . Str::random(6);
            }
            if (empty($room->admin_token)) {
                $room->admin_token = Str::random(32);
            }
        });
    }

    /**
     * Get all participants in this room
     */
    public function participants(): HasMany
    {
        return $this->hasMany(RoomParticipant::class);
    }

    /**
     * Get only online participants
     */
    public function onlineParticipants(): HasMany
    {
        return $this->hasMany(RoomParticipant::class)->where('is_online', true);
    }

    /**
     * Get all stories in this room
     */
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    /**
     * Get all votes in this room
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get all activities in this room
     */
    public function activities(): HasMany
    {
        return $this->hasMany(RoomActivity::class);
    }

    /**
     * Get the current active story (status = voting)
     */
    public function currentStory()
    {
        return $this->stories()->where('status', 'voting')->first();
    }

    /**
     * Get available card deck values
     */
    public function getCardDeckValues(): array
    {
        return match($this->card_deck) {
            'fibonacci' => ['0', '1', '2', '3', '5', '8', '13', '21', '34', '55', '89', '?', '☕'],
            'modified_fibonacci' => ['0', '½', '1', '2', '3', '5', '8', '13', '20', '40', '100', '?', '☕'],
            'tshirt' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '?', '☕'],
            'powers_of_2' => ['0', '1', '2', '4', '8', '16', '32', '64', '?', '☕'],
            default => ['0', '1', '2', '3', '5', '8', '13', '21', '?', '☕'],
        };
    }

    /**
     * Check if a token matches the admin token
     */
    public function isValidAdminToken(string $token): bool
    {
        return hash_equals($this->admin_token, $token);
    }

    /**
     * Scope: Find by slug
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
