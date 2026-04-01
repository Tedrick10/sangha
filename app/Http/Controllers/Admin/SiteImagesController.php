<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteImagesController extends Controller
{
    public function edit()
    {
        $images = [];
        foreach (SiteSetting::imageKeys() as $key => $config) {
            $images[$key] = [
                'label' => $config['label'],
                'hint' => $config['hint'],
                'current' => SiteSetting::imageUrl($key),
                'path' => SiteSetting::get($key),
            ];
        }
        return view('admin.site-images.edit', compact('images'));
    }

    public function update(Request $request)
    {
        $keys = array_keys(SiteSetting::imageKeys());
        $request->validate([
            'logo' => 'nullable|file|mimes:png,jpg,jpeg,svg,gif,webp|max:2048',
            'favicon' => 'nullable|file|mimes:png,jpg,jpeg,ico,gif,webp,x-icon|max:512',
            'og_image' => 'nullable|file|mimes:png,jpg,jpeg,gif,webp|max:2048',
            'apple_touch_icon' => 'nullable|file|mimes:png,jpg,jpeg,gif,webp|max:1024',
        ]);

        foreach ($keys as $key) {
            if ($request->hasFile($key)) {
                $this->deleteOld($key);
                $path = $request->file($key)->store('site-images', 'public');
                SiteSetting::set($key, $path);
            }
        }

        return redirect()->route('admin.site-images.edit')->with('success', t('site_images_updated'));
    }

    public function destroy(string $key)
    {
        if (!array_key_exists($key, SiteSetting::imageKeys())) {
            abort(404);
        }
        $this->deleteOld($key);
        SiteSetting::set($key, null);
        return redirect()->route('admin.site-images.edit')->with('success', t('site_image_removed'));
    }

    private function deleteOld(string $key): void
    {
        $path = SiteSetting::get($key);
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
