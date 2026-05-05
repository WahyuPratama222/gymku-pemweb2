<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

#[Fillable(['name', 'email', 'gender', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'Member';
    }

    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class, 'id_user', 'id_user');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'id_user', 'id_user');
    }

    /**
     * Cek apakah user memiliki membership premium yang aktif.
     * Paket premium = paket dengan is_premium = true, status registrasi 'Active',
     * dan pembayaran Lunas.
     */
    public function hasPremiumMembership(): bool
    {
        return $this->registrations()
            ->where('status', 'Active')
            ->whereHas('package', fn ($q) => $q->where('is_premium', true))
            ->whereHas('payments', fn ($q) => $q->where('payment_status', 'Lunas'))
            ->exists();
    }
}
