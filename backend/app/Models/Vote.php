<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $room_id
 * @property int $participant_id
 * @property int $story_id
 * @property string $vote
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read Room $room
 * @property-read RoomParticipant $participant
 * @property-read Story $story
 */
class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'participant_id',
        'story_id',
        'vote',
    ];

    /**
     * Get the room this vote belongs to
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the participant who cast this vote
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(RoomParticipant::class, 'participant_id');
    }

    /**
     * Get the story this vote is for
     */
    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    /**
     * Check if this is a special card (? or coffee)
     */
    public function isSpecialCard(): bool
    {
        return in_array($this->vote, ['?', '☕']);
    }

    /**
     * Check if this is the "don't know" card
     */
    public function isUnknown(): bool
    {
        return $this->vote === '?';
    }

    /**
     * Check if this is the "coffee break" card
     */
    public function isCoffeeBreak(): bool
    {
        return $this->vote === '☕';
    }

    /**
     * Check if this is a numeric vote
     */
    public function isNumeric(): bool
    {
        return is_numeric($this->vote);
    }

    /**
     * Get numeric value (if applicable)
     */
    public function getNumericValue(): ?float
    {
        return $this->isNumeric() ? floatval($this->vote) : null;
    }

    /**
     * Check if vote should be hidden (story not revealed)
     */
    public function shouldBeHidden(): bool
    {
        return !in_array($this->story->status, ['revealed', 'completed']);
    }

    /**
     * Get safe vote value (hidden or actual)
     */
    public function getSafeValue(): ?string
    {
        return $this->shouldBeHidden() ? null : $this->vote;
    }

    /**
     * Scope: For a specific room
     */
    public function scopeForRoom($query, string $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    /**
     * Scope: For a specific story
     */
    public function scopeForStory($query, int $storyId)
    {
        return $query->where('story_id', $storyId);
    }

    /**
     * Scope: By participant
     */
    public function scopeByParticipant($query, int $participantId)
    {
        return $query->where('participant_id', $participantId);
    }

    /**
     * Scope: Only numeric votes
     */
    public function scopeNumeric($query)
    {
        return $query->whereRaw('CAST(vote AS DECIMAL) IS NOT NULL')
            ->whereNotIn('vote', ['?', '☕']);
    }

    /**
     * Scope: Exclude special cards
     */
    public function scopeExcludeSpecialCards($query)
    {
        return $query->whereNotIn('vote', ['?', '☕']);
    }
}
