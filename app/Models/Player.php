<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'phone', 'skill_level', 'registered_at'])]
class Player extends Model
{
    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experience(): HasOne
    {
        return $this->hasOne(PlayerExperience::class);
    }

    public function pairingResults(): HasMany
    {
        return $this->hasMany(PairingResult::class);
    }
}
