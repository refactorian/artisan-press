<?php

namespace App\Models;

use App\Enums\MessageStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'name',
    'email',
    'subject',
    'message',
    'status',
    'read_at',
    'replied_at',
    'reply_notes',
    'ip_address',
])]
class ContactMessage extends Model
{
    use HasFactory, LogsActivity;

    protected function casts(): array
    {
        return [
            'status' => MessageStatus::class,
            'read_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'replied_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function markAsRead(): void
    {
        if ($this->status === MessageStatus::Unread) {
            $this->update([
                'status' => MessageStatus::Read,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsReplied(?string $notes = null): void
    {
        $this->update([
            'status' => MessageStatus::Replied,
            'replied_at' => now(),
            'reply_notes' => $notes ?? $this->reply_notes,
        ]);
    }

    public function archive(): void
    {
        $this->update([
            'status' => MessageStatus::Archived,
        ]);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', MessageStatus::Unread);
    }
}
