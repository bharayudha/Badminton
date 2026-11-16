<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pairing_session_id',
    'player_id',
    'partner_player_id',
    'team_number',
    'priority_score',
    'score_breakdown',
    'sort_position',
])]
class PairingResult extends Model
{
    protected function casts(): array
    {
        return [
            'priority_score' => 'decimal:3',
            'score_breakdown' => 'array',
            'team_number' => 'integer',
            'sort_position' => 'integer',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(PairingSession::class, 'pairing_session_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'partner_player_id');
    }
}
