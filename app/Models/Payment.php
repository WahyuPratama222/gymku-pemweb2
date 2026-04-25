<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

#[Fillable(['id_registration', 'payment_method', 'payment_status', 'payment_date', 'amount'])]
class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'id_payment';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'amount' => 'float',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'id_registration', 'id_registration');
    }

    /**
     * Scope untuk filter pembayaran yang sudah lunas
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'Lunas');
    }

    /**
     * Scope untuk filter pembayaran yang pending
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_status', 'Belum Lunas');
    }
}