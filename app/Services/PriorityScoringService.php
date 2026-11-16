<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class PriorityScoringService
{
    private const LEVEL_VALUES = [
        'belum_mengetahui' => 0,
        'pemula' => 1,
        'menengah' => 2,
        'mahir' => 3,
    ];

    /**
     * Score and sort players only; grouping policy is intentionally kept separate.
     *
     * @param  array{level: int, experience: int}  $weights
     * @return Collection<int, array{player: Player, score: float, breakdown: array<string, float>}>
     */
    public function scoreAndSort(Collection $players, array $weights): Collection
    {
        if (($weights['level'] + $weights['experience']) !== 100) {
            throw new InvalidArgumentException('Bobot level dan pengalaman harus berjumlah 100%.');
        }

        return $players
            ->map(function (Player $player) use ($weights): array {
                if ($player->experience?->experience_years === null) {
                    throw new InvalidArgumentException('Lama pengalaman pemain harus diisi sebelum menghitung skor prioritas.');
                }

                $levelValue = self::LEVEL_VALUES[$player->skill_level] ?? 0;
                $years = (float) $player->experience->experience_years;
                $levelScore = $weights['level'] / 100 * $levelValue;
                $experienceScore = $weights['experience'] / 100 * $years;

                return [
                    'player' => $player,
                    'score' => round($levelScore + $experienceScore, 3),
                    'breakdown' => [
                        'nilai_level' => (float) $levelValue,
                        'nilai_pengalaman_tahun' => $years,
                        'skor_level' => round($levelScore, 3),
                        'skor_pengalaman' => round($experienceScore, 3),
                    ],
                ];
            })
            ->sortBy([
                ['score', 'desc'],
                ['player.id', 'asc'],
            ])
            ->values();
    }
}
