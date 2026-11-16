<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'created_by_user_id',
    'mode',
    'status',
    'participant_count',
    'criteria_weights',
    'notes',
])]
class PairingSession extends Model
{
    protected function casts(): array
    {
        return [
            'criteria_weights' => 'array',
            'participant_count' => 'integer',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(PairingResult::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'pairing_results')
            ->withPivot(['partner_player_id', 'team_number', 'priority_score', 'score_breakdown', 'sort_position'])
            ->withTimestamps();
    }
}
