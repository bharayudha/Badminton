<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function index(Request $request): View
    {
        $players = Player::query()
            ->with(['user', 'experience'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            })
            ->when($request->filled('level'), fn ($query) => $query->where('skill_level', $request->string('level')->toString()))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('players.index', compact('players'));
    }

    public function create(): View
    {
        return view('players.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedPlayer($request);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $player = Player::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'] ?? null,
                'skill_level' => $validated['skill_level'],
            ]);

            $player->experience()->create($this->experienceData($validated));
        });

        return redirect()->route('players.index')->with('status', 'Data pemain berhasil ditambahkan.');
    }

    public function show(Player $player): View
    {
        return view('players.show', [
            'player' => $player->load(['user', 'experience']),
        ]);
    }

    public function edit(Player $player): View
    {
        return view('players.edit', [
            'player' => $player->load(['user', 'experience']),
        ]);
    }

    public function update(Request $request, Player $player): RedirectResponse
    {
        $validated = $this->validatedPlayer($request, $player);

        DB::transaction(function () use ($player, $validated): void {
            $player->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            $player->update([
                'phone' => $validated['phone'] ?? null,
                'skill_level' => $validated['skill_level'],
            ]);

            $player->experience()->updateOrCreate(
                ['player_id' => $player->id],
                $this->experienceData($validated),
            );
        });

        return redirect()->route('players.show', $player)->with('status', 'Data pemain berhasil diperbarui.');
    }

    public function destroy(Player $player): RedirectResponse
    {
        $player->user->delete();

        return redirect()->route('players.index')->with('status', 'Data pemain berhasil dihapus.');
    }

    private function validatedPlayer(Request $request, ?Player $player = null): array
    {
        $userId = $player?->user_id;

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => [$player ? 'nullable' : 'required', 'confirmed', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'skill_level' => ['required', Rule::in(['pemula', 'menengah', 'mahir', 'belum_mengetahui'])],
            'experience_years' => ['nullable', 'numeric', 'min:0', 'max:999.9'],
            'weekly_frequency' => ['required', 'integer', 'min:0', 'max:14'],
            'training_club_experience' => ['nullable', 'string', 'max:5000'],
            'competition_experience' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function experienceData(array $validated): array
    {
        return [
            'experience_years' => $validated['experience_years'] ?? null,
            'weekly_frequency' => $validated['weekly_frequency'],
            'training_club_experience' => $validated['training_club_experience'] ?? null,
            'competition_experience' => $validated['competition_experience'] ?? null,
        ];
    }
}
