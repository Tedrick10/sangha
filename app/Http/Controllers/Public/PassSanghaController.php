<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\View\View;

class PassSanghaController extends Controller
{
    public function index(): View
    {
        $snapshotRaw = SiteSetting::get('pass_sanghas_snapshot');
        $snapshot = $snapshotRaw ? json_decode($snapshotRaw, true) : null;

        $passSanghas = collect($snapshot['pass_sanghas'] ?? []);
        $generatedAt = $snapshot['generated_at'] ?? null;

        return view('website.pass-sanghas', compact('passSanghas', 'generatedAt'));
    }
}

