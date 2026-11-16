<?php

namespace Tests\Feature;

use App\Models\PairingSession;
use App\Models\Player;
use App\Models\PlayerExperience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PairingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_provisional_partner_pairs_with_explicit_weights(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $players = $this->makePlayers(4);

        $response = $this->actingAs($admin)->post(route('pairing.store'), [
            'mode' => 'pencarian_partner',
            'player_ids' => $players->pluck('id')->all(),
            'weights' => ['level' => '60', 'experience' => '40'],
            'notes' => 'Sesi uji',
        ]);

        $session = PairingSession::firstOrFail();
        $response->assertRedirect(route('pairing.show', $session));
        $this->assertSame(4, $session->results()->count());
        $this->assertSame(4, $session->results()->whereNotNull('partner_player_id')->count());
        $this->assertNotNull($session->results()->first()->priority_score);
        $this->actingAs($admin)->get(route('pairing.show', $session))
            ->assertOk()
            ->assertSee('bukan klaim optimal')
            ->assertSee('Sesi uji');
    }

    public function test_balanced_teams_follow_the_documented_repeating_snake_pattern(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $players = $this->makePlayers(8);

        $this->actingAs($admin)->post(route('pairing.store'), [
            'mode' => 'tim_seimbang',
            'player_ids' => $players->pluck('id')->all(),
            'weights' => ['level' => 50, 'experience' => 50],
        ])->assertRedirect();

        $session = PairingSession::firstOrFail();
        $this->assertSame(4, $session->results()->where('team_number', 1)->count());
        $this->assertSame(4, $session->results()->where('team_number', 2)->count());
        $teamNumbers = $session->results()->orderBy('sort_position')->pluck('team_number')->all();
        $this->assertSame([1, 2, 2, 1, 1, 2, 2, 1], $teamNumbers);
    }

    public function test_pairing_rejects_non_total_weights_and_odd_partner_selection(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $players = $this->makePlayers(3);

        $this->actingAs($admin)->from(route('pairing.create'))->post(route('pairing.store'), [
            'mode' => 'pencarian_partner',
            'player_ids' => $players->pluck('id')->all(),
            'weights' => ['level' => 60, 'experience' => 30],
        ])->assertRedirect(route('pairing.create'))
            ->assertSessionHasErrors(['weights']);

        $this->actingAs($admin)->from(route('pairing.create'))->post(route('pairing.store'), [
            'mode' => 'pencarian_partner',
            'player_ids' => $players->pluck('id')->all(),
            'weights' => ['level' => 60, 'experience' => 40],
        ])->assertRedirect(route('pairing.create'))
            ->assertSessionHasErrors(['player_ids']);

        $this->assertDatabaseCount('pairing_sessions', 0);
    }

    public function test_pairing_requires_experience_years_for_every_selected_player(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $players = $this->makePlayers(2);
        $players->first()->experience->update(['experience_years' => null]);

        $this->actingAs($admin)->from(route('pairing.create'))->post(route('pairing.store'), [
            'mode' => 'pencarian_partner',
            'player_ids' => $players->pluck('id')->all(),
            'weights' => ['level' => 60, 'experience' => 40],
        ])->assertRedirect(route('pairing.create'))
            ->assertSessionHasErrors(['player_ids']);

        $this->assertDatabaseCount('pairing_sessions', 0);
    }

    private function makePlayers(int $count): Collection
    {
        return collect(range(1, $count))->map(function (int $number): Player {
            $user = User::factory()->create(['name' => "Pemain {$number}"]);
            $player = Player::create([
                'user_id' => $user->id,
                'skill_level' => ['pemula', 'menengah', 'mahir'][$number % 3],
            ]);
            PlayerExperience::create([
                'player_id' => $player->id,
                'experience_years' => $number,
                'weekly_frequency' => 2,
            ]);

            return $player;
        });
    }
}
