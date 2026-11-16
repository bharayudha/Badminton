<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\PlayerExperience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAndPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_a_player_account_without_admin_privileges(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Pemain Baru',
            'email' => 'baru@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $user = User::where('email', 'baru@example.test')->firstOrFail();
        $response->assertRedirect(route('profile.edit'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('pemain', $user->role);
        $this->assertSame('belum_mengetahui', $user->player->skill_level);
        $this->assertDatabaseHas('player_experiences', ['player_id' => $user->player->id]);
    }

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['password' => 'kata-sandi-uji']);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'kata-sandi-uji',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_player_cannot_access_admin_player_management(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'pemain'])->save();

        $this->actingAs($user)
            ->get(route('players.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_and_filter_a_player(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)->post(route('players.store'), [
            'name' => 'Sari Pemain',
            'email' => 'sari@example.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'phone' => '08123456789',
            'skill_level' => 'menengah',
            'experience_years' => '2.5',
            'weekly_frequency' => '2',
            'training_club_experience' => 'Klub kampus',
            'competition_experience' => '',
        ])->assertRedirect(route('players.index'));

        $player = Player::whereHas('user', fn ($query) => $query->where('email', 'sari@example.test'))->firstOrFail();
        $this->assertSame(2.5, (float) $player->experience->experience_years);
        $this->actingAs($admin)
            ->get(route('players.index', ['search' => 'Sari', 'level' => 'menengah']))
            ->assertOk()
            ->assertSee('Sari Pemain');
    }

    public function test_player_can_update_only_their_own_profile(): void
    {
        $user = User::factory()->create();
        $player = Player::create([
            'user_id' => $user->id,
            'skill_level' => 'pemula',
        ]);
        PlayerExperience::create(['player_id' => $player->id]);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama Diperbarui',
            'email' => $user->email,
            'phone' => '0811222333',
            'skill_level' => 'menengah',
            'experience_years' => '3',
            'weekly_frequency' => '2',
            'training_club_experience' => 'Latihan rutin',
            'competition_experience' => '',
        ])->assertRedirect(route('profile.edit'));

        $this->assertSame('Nama Diperbarui', $user->fresh()->name);
        $this->assertSame('menengah', $player->fresh()->skill_level);
        $this->assertSame('Latihan rutin', $player->experience->fresh()->training_club_experience);
    }

    public function test_admin_can_change_roles_but_cannot_remove_their_own_admin_access(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $target = User::factory()->create();

        $this->actingAs($admin)->put(route('users.update', $target), [
            'role' => 'admin',
        ])->assertRedirect();
        $this->assertSame('admin', $target->fresh()->role);

        $this->actingAs($admin)->from(route('users.index'))->put(route('users.update', $admin), [
            'role' => 'pemain',
        ])->assertRedirect(route('users.index'))
            ->assertSessionHasErrors('role');
        $this->assertSame('admin', $admin->fresh()->role);
    }
}
