<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = ['id_user', 'record_date', 'weight', 'height', 'body_fat', 'muscle_mass'];

    protected $table = 'progress';
    protected $primaryKey = 'id_progress';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'weight' => 'float',
            'height' => 'float',
            'body_fat' => 'float',
            'muscle_mass' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}