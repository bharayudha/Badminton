<?php

namespace App\Http\Controllers;

use App\Models\PairingSession;
use App\Models\Player;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $recentSessions = PairingSession::query()
            ->with('createdBy')
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard', [
            'totalPlayers' => Player::count(),
            'beginnerPlayers' => Player::where('skill_level', 'pemula')->count(),
            'intermediatePlayers' => Player::where('skill_level', 'menengah')->count(),
            'advancedPlayers' => Player::where('skill_level', 'mahir')->count(),
            'pairingSessionCount' => PairingSession::count(),
            'recentSessions' => $recentSessions,
        ]);
    }
}
