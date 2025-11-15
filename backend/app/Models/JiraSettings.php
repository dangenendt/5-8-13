<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JiraSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jira_domain',
        'jira_email',
        'jira_api_token',
        'jira_project_key',
        'is_active',
    ];

    protected $hidden = [
        'jira_api_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the Jira settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Encrypt the API token before saving.
     */
    public function setJiraApiTokenAttribute($value): void
    {
        $this->attributes['jira_api_token'] = encrypt($value);
    }

    /**
     * Decrypt the API token when accessing.
     */
    public function getJiraApiTokenAttribute($value): ?string
    {
        try {
            return $value ? decrypt($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the full Jira URL.
     */
    public function getJiraUrlAttribute(): string
    {
        return "https://{$this->jira_domain}";
    }

    /**
     * Get the active Jira settings for a user.
     */
    public static function getActiveForUser(?int $userId): ?self
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->first();
    }
}
