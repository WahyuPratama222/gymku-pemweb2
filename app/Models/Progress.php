<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['id_user', 'record_date', 'weight', 'height', 'body_fat', 'muscle_mass', 'chest', 'waist', 'biceps', 'thigh'])]
class Progress extends Model
{
    use HasFactory;
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
            'chest' => 'float',
            'waist' => 'float',
            'biceps' => 'float',
            'thigh' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}