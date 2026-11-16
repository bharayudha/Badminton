<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'player_id',
    'experience_years',
    'weekly_frequency',
    'training_club_experience',
    'competition_experience',
])]
class PlayerExperience extends Model
{
    protected function casts(): array
    {
        return [
            'experience_years' => 'decimal:1',
            'weekly_frequency' => 'integer',
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
