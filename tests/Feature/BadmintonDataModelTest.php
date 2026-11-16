<?php

namespace Tests\Feature;

use App\Models\PairingResult;
use App\Models\PairingSession;
use App\Models\Player;
use App\Models\PlayerExperience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BadmintonDataModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_experience_and_pairing_results_are_related(): void
    {
        $user = User::create([
            'name' => 'Pemain Uji',
            'email' => 'pemain@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $player = Player::create([
            'user_id' => $user->id,
            'skill_level' => 'menengah',
        ]);
        $experience = PlayerExperience::create([
            'player_id' => $player->id,
            'experience_years' => 3.5,
            'weekly_frequency' => 2,
            'training_club_experience' => 'Latihan klub',
        ]);
        $session = PairingSession::create([
            'created_by_user_id' => $user->id,
            'mode' => 'pencarian_partner',
            'criteria_weights' => ['level' => null, 'pengalaman' => null],
        ]);
        $result = PairingResult::create([
            'pairing_session_id' => $session->id,
            'player_id' => $player->id,
            'priority_score' => 12.345,
            'score_breakdown' => ['level' => 5, 'pengalaman' => 7.345],
        ]);

        $this->assertSame('pemain', $user->fresh()->role);
        $this->assertTrue($user->player->is($player));
        $this->assertTrue($player->experience->is($experience));
        $this->assertTrue($experience->player->is($player));
        $this->assertTrue($session->players->first()->is($player));
        $this->assertTrue($result->session->is($session));
        $this->assertTrue($result->player->is($player));
        $this->assertSame(3.5, (float) $player->experience->experience_years);
        $this->assertSame(['level' => 5, 'pengalaman' => 7.345], $result->fresh()->score_breakdown);
    }
}
