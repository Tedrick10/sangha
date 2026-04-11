<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AppearancePortals;
use Illuminate\Http\Request;

class AppearanceController extends Controller
{
    public function edit()
    {
        $portals = [];
        foreach (['website', 'admin', 'monastery'] as $p) {
            $portals[$p] = AppearancePortals::portal($p);
        }

        return view('admin.appearance.edit', [
            'portals' => $portals,
            'appearancePresets' => AppearancePortals::presets(),
        ]);
    }

    public function update(Request $request)
    {
        $portals = $request->input('portals', []);
        if (is_array($portals)) {
            foreach (['website', 'admin', 'monastery'] as $portal) {
                if (! isset($portals[$portal]) || ! is_array($portals[$portal])) {
                    continue;
                }
                foreach (AppearancePortals::fields() as $f) {
                    if (! array_key_exists($f, $portals[$portal])) {
                        continue;
                    }
                    $v = trim((string) ($portals[$portal][$f] ?? ''));
                    $portals[$portal][$f] = $v === '' ? null : $v;
                }
            }
            $request->merge(['portals' => $portals]);
        }

        $hex = ['nullable', 'string', 'max:16', 'regex:/^#([0-9A-Fa-f]{6})$/'];
        $rules = ['portals' => 'nullable|array'];
        foreach (['website', 'admin', 'monastery'] as $portal) {
            foreach (AppearancePortals::fields() as $f) {
                $rules['portals.'.$portal.'.'.$f] = $hex;
            }
        }
        $request->validate($rules);

        AppearancePortals::saveFromRequest($request->input('portals', []));

        return redirect()->route('admin.appearance.edit')->with('success', t('appearance_colors_saved', 'Appearance colors saved.'));
    }
}
