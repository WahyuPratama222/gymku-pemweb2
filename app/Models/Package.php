<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

#[Fillable(['name', 'image', 'price', 'day_duration', 'is_premium', 'status'])]
class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';
    protected $primaryKey = 'id_package';
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'day_duration' => 'integer',
            'is_premium' => 'boolean',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'id_package', 'id_package');
    }

    /**
     * Scope untuk filter paket aktif (sesuai dengan enum di migration: 'Active')
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'Active');
    }
}
