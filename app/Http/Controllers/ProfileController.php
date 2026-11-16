<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $player = $request->user()->player()->with('experience')->first();

        if (! $player) {
            return view('profile.account');
        }

        return view('profile.edit', compact('player'));
    }

    public function update(Request $request): RedirectResponse
    {
        $player = $request->user()->player()->with('experience')->first();
        if (! $player) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->user()->id)],
            ]);

            $request->user()->update($validated);

            return redirect()->route('profile.edit')->with('status', 'Profil akun berhasil diperbarui.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->user()->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'skill_level' => ['required', Rule::in(['pemula', 'menengah', 'mahir', 'belum_mengetahui'])],
            'experience_years' => ['nullable', 'numeric', 'min:0', 'max:999.9'],
            'weekly_frequency' => ['required', 'integer', 'min:0', 'max:14'],
            'training_club_experience' => ['nullable', 'string', 'max:5000'],
            'competition_experience' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($request, $player, $validated): void {
            $request->user()->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);
            $player->update([
                'phone' => $validated['phone'] ?? null,
                'skill_level' => $validated['skill_level'],
            ]);
            $player->experience()->updateOrCreate(
                ['player_id' => $player->id],
                [
                    'experience_years' => $validated['experience_years'] ?? null,
                    'weekly_frequency' => $validated['weekly_frequency'],
                    'training_club_experience' => $validated['training_club_experience'] ?? null,
                    'competition_experience' => $validated['competition_experience'] ?? null,
                ],
            );
        });

        return redirect()->route('profile.edit')->with('status', 'Profil berhasil diperbarui.');
    }
}
