<?php

namespace App\Models;

use App\Enums\RedirectType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'source_path',
    'target_path',
    'status_code',
    'is_active',
    'hit_count',
    'last_accessed_at',
])]
class Redirect extends Model
{
    use HasFactory, LogsActivity;

    protected function casts(): array
    {
        return [
            'status_code' => RedirectType::class,
            'is_active' => 'boolean',
            'hit_count' => 'integer',
            'last_accessed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['source_path', 'target_path', 'status_code', 'is_active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function recordHit(): void
    {
        $this->increment('hit_count');
        $this->updateQuietly(['last_accessed_at' => now()]);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
