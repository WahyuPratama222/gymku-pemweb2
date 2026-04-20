<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}