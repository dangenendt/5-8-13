<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $room_id
 * @property string $title
 * @property string|null $description
 * @property string|null $3rd_party_ident
 * @property string|null $3rd_party_url
 * @property string|null $final_estimate
 * @property int $sort_order
 * @property Carbon|null $voting_started_at
 * @property Carbon|null $revealed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Room $room
 * @property-read Collection|Vote[] $votes
 */
class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'title',
        'description',
        '3rd_party_ident',
        '3rd_party_url',
        'final_estimate',
        'final_estimate',
        'sort_order',
        'voting_started_at',
        'revealed_at',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'voting_started_at' => 'datetime',
        'revealed_at' => 'datetime',
    ];

    protected $attributes = [
        'sort_order' => 0,
    ];

    /**
     * Get the room this story belongs to
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get all votes for this story
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Check if voting has started
     */
    public function isVoting(): bool
    {
        return !is_null($this->voting_started_at) && is_null($this->revealed_at) && is_null($this->final_estimate);
    }

    /**
     * Check if votes are revealed
     */
    public function isRevealed(): bool
    {
        return !is_null($this->revealed_at);
    }

    /**
     * Check if story is completed (has final estimate)
     */
    public function isCompleted(): bool
    {
        return !is_null($this->final_estimate);
    }

    /**
     * Check if story is pending (not started yet)
     */
    public function isPending(): bool
    {
        return is_null($this->voting_started_at);
    }

    /**
     * Start voting on this story
     */
    public function startVoting(): self
    {
        // Set other voting stories in room to revealed or pending
        $this->room->stories()
            ->whereNotNull('voting_started_at')
            ->whereNull('revealed_at')
            ->whereNull('final_estimate')
            ->where('id', '!=', $this->id)
            ->each(function ($story) {
                if ($story->votes()->exists()) {
                    $story->revealVotes();
                } else {
                    $story->update(['voting_started_at' => null]);
                }
            });

        $this->update([
            'voting_started_at' => now(),
            'revealed_at' => null,
            'final_estimate' => null,
        ]);

        return $this->fresh();
    }

    /**
     * Reveal all votes for this story
     */
    public function revealVotes(): self
    {
        if (!$this->isRevealed()) {
            $this->update([
                'revealed_at' => now(),
            ]);
        }

        return $this->fresh();
    }

    /**
     * Complete story with final estimate
     */
    public function complete(?string $estimate = null): self
    {
        // If no estimate provided, use consensus or average
        if (is_null($estimate)) {
            $estimate = $this->calculateSuggestedEstimate();
        }

        $this->update([
            'final_estimate' => $estimate,
            'revealed_at' => $this->revealed_at ?? now(),
        ]);

        return $this->fresh();
    }

    /**
     * Reset voting (clear all votes and restart)
     */
    public function resetVoting(): self
    {
        $this->votes()->delete();

        $this->update([
            'voting_started_at' => now(),
            'revealed_at' => null,
            'final_estimate' => null,
        ]);

        return $this->fresh();
    }

    /**
     * Skip story (mark as completed without estimate)
     */
    public function skip(): self
    {
        $this->update([
            'voting_started_at' => $this->voting_started_at ?? now(),
            'revealed_at' => $this->revealed_at ?? now(),
            'final_estimate' => 'skipped',
        ]);

        return $this->fresh();
    }

    /**
     * Check if all voters have voted
     */
    public function allVoted(): bool
    {
        $voterCount = $this->room->participants()
            ->voters()
            ->online()
            ->count();

        $voteCount = $this->votes()->count();

        return $voterCount > 0 && $voterCount === $voteCount;
    }

    /**
     * Get number of votes cast
     */
    public function getVoteCount(): int
    {
        return $this->votes()->count();
    }

    /**
     * Get number of voters who haven't voted yet
     */
    public function getMissingVoteCount(): int
    {
        $voterCount = $this->room->participants()
            ->voters()
            ->online()
            ->count();

        return max(0, $voterCount - $this->getVoteCount());
    }

    /**
     * Calculate suggested estimate based on votes
     */
    public function calculateSuggestedEstimate(): ?string
    {
        $stats = $this->getVoteStatistics();

        if (empty($stats)) {
            return null;
        }

        // If consensus, return that value
        if ($stats['consensus'] ?? false) {
            return (string) $stats['votes'][0];
        }

        // Return most common vote
        if (isset($stats['mode'])) {
            return (string) $stats['mode'];
        }

        // Return average rounded to nearest fibonacci number
        if (isset($stats['average'])) {
            return $this->roundToFibonacci($stats['average']);
        }

        return null;
    }

    /**
     * Get vote statistics
     */
    public function getVoteStatistics(): array
    {
        if (!$this->isRevealed() && !$this->isCompleted()) {
            return [];
        }

        $allVotes = $this->votes()->with('participant')->get();

        if ($allVotes->isEmpty()) {
            return [];
        }

        $numericVotes = $allVotes->filter(fn($vote) => $vote->isNumeric());
        $specialVotes = $allVotes->filter(fn($vote) => $vote->isSpecialCard());

        $stats = [
            'total_votes' => $allVotes->count(),
            'votes' => $allVotes->pluck('vote')->toArray(),
            'voters' => $allVotes->map(fn($vote) => [
                'name' => $vote->participant->name,
                'vote' => $vote->vote,
                'avatar_emoji' => $vote->participant->avatar_emoji,
            ])->toArray(),
        ];

        if ($numericVotes->isNotEmpty()) {
            $values = $numericVotes->pluck('vote')->map(fn($v) => floatval($v));

            $stats['average'] = round($values->average(), 1);
            $stats['min'] = $values->min();
            $stats['max'] = $values->max();
            $stats['consensus'] = $values->unique()->count() === 1;

            // Calculate mode (most common value)
            $valueCounts = $values->countBy();
            $maxCount = $valueCounts->max();
            $modes = $valueCounts->filter(fn($count) => $count === $maxCount)->keys();

            if ($modes->count() === 1) {
                $stats['mode'] = $modes->first();
            }
        }

        if ($specialVotes->isNotEmpty()) {
            $stats['special_cards'] = [
                'unknown' => $specialVotes->filter(fn($v) => $v->vote === '?')->count(),
                'coffee' => $specialVotes->filter(fn($v) => $v->vote === '☕')->count(),
            ];
        }

        return $stats;
    }

    /**
     * Round value to nearest fibonacci number
     */
    private function roundToFibonacci(float $value): string
    {
        $fibonacci = [0, 0.5, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89];

        $closest = $fibonacci[0];
        $minDiff = abs($value - $closest);

        foreach ($fibonacci as $fib) {
            $diff = abs($value - $fib);
            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closest = $fib;
            }
        }

        return (string) $closest;
    }

    /**
     * Get voting progress percentage
     */
    public function getVotingProgress(): int
    {
        $voterCount = $this->room->participants()
            ->voters()
            ->online()
            ->count();

        if ($voterCount === 0) {
            return 0;
        }

        return (int) round(($this->getVoteCount() / $voterCount) * 100);
    }

    /**
     * Get time elapsed since voting started
     */
    public function getVotingDuration(): ?int
    {
        if (is_null($this->voting_started_at)) {
            return null;
        }

        $end = $this->revealed_at ?? now();

        return $this->voting_started_at->diffInSeconds($end);
    }

    /**
     * Scope: Ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    /**
     * Scope: Pending stories
     */
    public function scopePending($query)
    {
        return $query->whereNull('voting_started_at');
    }

    /**
     * Scope: Currently voting
     */
    public function scopeVoting($query)
    {
        return $query->whereNotNull('voting_started_at')
            ->whereNull('revealed_at')
            ->whereNull('final_estimate');
    }

    /**
     * Scope: Revealed stories
     */
    public function scopeRevealed($query)
    {
        return $query->whereNotNull('revealed_at')
            ->whereNull('final_estimate');
    }

    /**
     * Scope: Completed stories
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('final_estimate');
    }

    /**
     * Scope: Active (voting or revealed, not completed)
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('voting_started_at')
            ->whereNull('final_estimate');
    }

    /**
     * Scope: With 3rd party integration
     */
    public function scopeWith3rdPartyIdent($query)
    {
        return $query->whereNotNull('3rd_party_ident');
    }

    /**
     * Scope: For specific room
     */
    public function scopeForRoom($query, string $roomId)
    {
        return $query->where('room_id', $roomId);
    }
}
