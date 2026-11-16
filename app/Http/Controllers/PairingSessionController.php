<?php

namespace App\Http\Controllers;

use App\Models\PairingResult;
use App\Models\PairingSession;
use App\Models\Player;
use App\Services\PriorityScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PairingSessionController extends Controller
{
    public function index(): View
    {
        $sessions = PairingSession::query()
            ->with('createdBy')
            ->withCount('results')
            ->latest()
            ->paginate(10);

        return view('pairing.index', compact('sessions'));
    }

    public function create(): View
    {
        $players = Player::query()
            ->with(['user', 'experience'])
            ->orderBy('skill_level')
            ->orderBy('id')
            ->get();

        return view('pairing.create', compact('players'));
    }

    public function store(Request $request, PriorityScoringService $scoring): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', Rule::in(['pencarian_partner', 'tim_seimbang'])],
            'player_ids' => ['required', 'array', 'min:2'],
            'player_ids.*' => ['required', 'integer', 'distinct', 'exists:players,id'],
            'weights.level' => ['required', 'integer', 'min:0', 'max:100'],
            'weights.experience' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'player_ids.min' => 'Pilih setidaknya dua pemain.',
            'player_ids.*.distinct' => 'Pemain tidak boleh dipilih lebih dari sekali.',
        ]);

        if (array_sum($validated['weights']) !== 100) {
            return back()->withInput()->withErrors([
                'weights' => 'Bobot level dan pengalaman harus berjumlah tepat 100%.',
            ]);
        }

        $playerCount = count($validated['player_ids']);
        if ($validated['mode'] === 'pencarian_partner' && $playerCount % 2 !== 0) {
            return back()->withInput()->withErrors([
                'player_ids' => 'Mode pencarian partner memerlukan jumlah pemain genap.',
            ]);
        }

        if ($validated['mode'] === 'tim_seimbang' && ($playerCount < 4 || $playerCount % 4 !== 0)) {
            return back()->withInput()->withErrors([
                'player_ids' => 'Pilih minimal empat pemain dan jumlahnya harus kelipatan empat untuk membentuk dua tim ganda yang setara.',
            ]);
        }

        $selectedIds = array_map('intval', $validated['player_ids']);
        $players = Player::query()
            ->with(['user', 'experience'])
            ->whereIn('id', $validated['player_ids'])
            ->get()
            ->sortBy(fn (Player $player): int => array_search($player->id, $selectedIds, true))
            ->values();

        $missingExperience = $players
            ->filter(fn (Player $player): bool => $player->experience?->experience_years === null)
            ->pluck('user.name')
            ->all();

        if ($missingExperience !== []) {
            return back()->withInput()->withErrors([
                'player_ids' => 'Isi lama pengalaman (tahun) terlebih dahulu untuk: '.implode(', ', $missingExperience).'.',
            ]);
        }

        $scoredPlayers = $scoring->scoreAndSort($players, $validated['weights']);

        $session = DB::transaction(function () use ($request, $validated, $playerCount, $scoredPlayers): PairingSession {
            $session = PairingSession::create([
                'created_by_user_id' => $request->user()->id,
                'mode' => $validated['mode'],
                'status' => 'selesai',
                'participant_count' => $playerCount,
                'criteria_weights' => $validated['weights'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $ordered = $scoredPlayers->all();
            $partnerIds = [];
            $teamNumbers = [];

            if ($validated['mode'] === 'pencarian_partner') {
                for ($index = 0; $index < count($ordered); $index += 2) {
                    $firstId = $ordered[$index]['player']->id;
                    $secondId = $ordered[$index + 1]['player']->id;
                    $partnerIds[$firstId] = $secondId;
                    $partnerIds[$secondId] = $firstId;
                }
            } else {
                $snakeDraft = [1, 2, 2, 1];
                foreach ($ordered as $index => $item) {
                    $teamNumbers[$item['player']->id] = $snakeDraft[$index % count($snakeDraft)];
                }
            }

            foreach ($ordered as $position => $item) {
                PairingResult::create([
                    'pairing_session_id' => $session->id,
                    'player_id' => $item['player']->id,
                    'partner_player_id' => $partnerIds[$item['player']->id] ?? null,
                    'team_number' => $teamNumbers[$item['player']->id] ?? null,
                    'priority_score' => $item['score'],
                    'score_breakdown' => $item['breakdown'],
                    'sort_position' => $position + 1,
                ]);
            }

            return $session;
        });

        return redirect()->route('pairing.show', $session)->with('status', 'Pengaturan berbasis skor prioritas berhasil dibuat. Hasil ini bersifat awal dan bukan klaim pasangan optimal.');
    }

    public function show(PairingSession $pairingSession): View
    {
        $pairingSession->load(['createdBy', 'results.player.user', 'results.partner.user']);

        return view('pairing.show', ['session' => $pairingSession]);
    }
}
