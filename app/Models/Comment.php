<?php

namespace App\Models;

use App\Enums\CommentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'post_id',
    'user_id',
    'parent_id',
    'guest_name',
    'guest_email',
    'guest_website',
    'content',
    'status',
    'ip_address',
    'user_agent',
    'moderated_by',
    'moderated_at',
])]
class Comment extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => CommentStatus::class,
            'moderated_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'content', 'post_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Anonymous';
    }

    public function getAuthorEmailAttribute(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    public function approve(?User $moderator = null): void
    {
        $this->update([
            'status' => CommentStatus::Approved,
            'moderated_by' => $moderator?->id ?? auth()->id(),
            'moderated_at' => now(),
        ]);
    }

    public function reject(?User $moderator = null): void
    {
        $this->update([
            'status' => CommentStatus::Rejected,
            'moderated_by' => $moderator?->id ?? auth()->id(),
            'moderated_at' => now(),
        ]);
    }

    public function markAsSpam(?User $moderator = null): void
    {
        $this->update([
            'status' => CommentStatus::Spam,
            'moderated_by' => $moderator?->id ?? auth()->id(),
            'moderated_at' => now(),
        ]);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::Approved);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::Pending);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
