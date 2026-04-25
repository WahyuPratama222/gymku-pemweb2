<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

#[Fillable(['id_user', 'id_package', 'registration_date', 'start_date', 'expiry_date', 'status'])]
class Registration extends Model
{
    use HasFactory;

    protected $table = 'registrations';
    protected $primaryKey = 'id_registration';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'registration_date' => 'datetime',
            'start_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'id_package', 'id_package');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'id_registration', 'id_registration');
    }

    /**
     * Accessor untuk payment (single) - untuk compatibility
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'id_registration', 'id_registration');
    }

    /**
     * Accessor untuk mendapatkan sisa hari membership
     */
    protected function daysRemaining(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->expiry_date || $this->status !== 'Active') {
                    return 0;
                }

                $today = Carbon::today();
                $expiry = Carbon::parse($this->expiry_date);
                $diff = $today->diffInDays($expiry, false);

                return $diff < 0 ? 0 : (int) $diff;
            }
        );
    }

    /**
     * Accessor untuk mendapatkan nama paket (untuk compatibility dengan controller)
     */
    protected function packageName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->package?->name
        );
    }

    /**
     * Accessor untuk mendapatkan harga paket (untuk compatibility dengan controller)
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->package?->price
        );
    }

    /**
     * Accessor untuk mendapatkan durasi hari paket (untuk compatibility dengan controller)
     */
    protected function dayDuration(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->package?->day_duration
        );
    }
}