<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\PlayerExperience;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => env('SEED_ADMIN_EMAIL', 'admin@badminton.test')],
            [
                'name' => 'Administrator',
                'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'password')),
            ],
        );
        $admin->forceFill(['role' => 'admin'])->save();

        $samples = [
            ['name' => 'Andi Pratama', 'email' => 'andi@badminton.test', 'skill_level' => 'pemula', 'years' => 1.0, 'frequency' => 1],
            ['name' => 'Budi Santoso', 'email' => 'budi@badminton.test', 'skill_level' => 'menengah', 'years' => 3.0, 'frequency' => 2],
            ['name' => 'Citra Lestari', 'email' => 'citra@badminton.test', 'skill_level' => 'mahir', 'years' => 6.0, 'frequency' => 3],
            ['name' => 'Dimas Saputra', 'email' => 'dimas@badminton.test', 'skill_level' => 'menengah', 'years' => 2.5, 'frequency' => 2],
        ];

        foreach ($samples as $sample) {
            $user = User::firstOrCreate(
                ['email' => $sample['email']],
                [
                    'name' => $sample['name'],
                    'password' => Hash::make('password'),
                    'role' => 'pemain',
                ],
            );
            $player = Player::firstOrCreate(
                ['user_id' => $user->id],
                ['skill_level' => $sample['skill_level']],
            );
            PlayerExperience::firstOrCreate(
                ['player_id' => $player->id],
                [
                    'experience_years' => $sample['years'],
                    'weekly_frequency' => $sample['frequency'],
                ],
            );
        }
    }
}
