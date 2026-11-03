<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'name', 'batch_number', 'quantity', 'unit',
        'manufacturing_date', 'expiry_date', 'description', 'disposal_status',
        'disposed_at', 'alert_dismissed_at',
    ];

    protected function casts(): array
    {
        return [
            'manufacturing_date' => 'date',
            'expiry_date' => 'date',
            'disposed_at' => 'datetime',
            'alert_dismissed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getDaysRemainingAttribute(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->expiry_date->startOfDay(), false);
    }

    public function getExpiryStatusAttribute(): string
    {
        return match (true) {
            $this->days_remaining < 0 => 'expired',
            $this->days_remaining <= 30 => 'expiring_soon',
            default => 'safe',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->expiry_status) {
            'expired' => 'Expired',
            'expiring_soon' => 'Expiring Soon',
            default => 'Safe',
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->expiry_status) {
            'expired' => 'danger',
            'expiring_soon' => 'warning',
            default => 'success',
        };
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isAdmin() ? $query : $query->where('user_id', $user->id);
    }

    public function scopeAlerts(Builder $query): Builder
    {
        return $query->whereDate('expiry_date', '<=', now()->addDays(30)->toDateString());
    }
}
