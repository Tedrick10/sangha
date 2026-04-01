<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Support\Facades\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TranslationController extends Controller
{
    public function edit(Language $language): View
    {
        $keys = $this->collectTranslationKeys();
        $translations = $language->translations()->pluck('value', 'key')->toArray();

        $otherLanguages = Language::query()
            ->where('id', '!=', $language->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $translationsByLanguage = [];
        foreach ($otherLanguages as $lang) {
            $translationsByLanguage[$lang->id] = [
                'language' => $lang,
                'values' => $lang->translations()->pluck('value', 'key')->toArray(),
            ];
        }

        return view('admin.translations.edit', compact('language', 'keys', 'translations', 'otherLanguages', 'translationsByLanguage'));
    }

    public function update(Request $request, Language $language): RedirectResponse
    {
        $keys = $this->collectTranslationKeys();
        $input = $request->input('translations', []);

        foreach ($keys as $key => $default) {
            $value = $input[$key] ?? '';
            if ($value !== '') {
                $language->setTranslation($key, $value);
            } else {
                $language->translations()->where('key', $key)->delete();
            }
        }

        return redirect()
            ->route('admin.translations.edit', $language)
            ->with('success', 'Translations saved successfully.');
    }

    /**
     * @return array<string, string>
     */
    private function collectTranslationKeys(): array
    {
        $keys = config('translation-keys', []);
        $paths = [
            resource_path('views'),
            app_path(),
        ];

        $pattern = "/t\\(\\s*['\"]([^'\"]+)['\"]\\s*(?:,\\s*['\"]([^'\"]*)['\"])?\\s*\\)/";

        foreach ($paths as $path) {
            if (! File::isDirectory($path)) {
                continue;
            }

            foreach (File::allFiles($path) as $file) {
                $content = File::get($file->getRealPath());
                if (! preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                    continue;
                }

                foreach ($matches as $match) {
                    $key = $match[1] ?? null;
                    if (! $key) {
                        continue;
                    }
                    $default = $match[2] ?? ($keys[$key] ?? $key);
                    if (! array_key_exists($key, $keys)) {
                        $keys[$key] = $default;
                    }
                }
            }
        }

        ksort($keys);

        return $keys;
    }
}
