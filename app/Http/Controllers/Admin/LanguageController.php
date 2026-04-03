<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LanguageController extends Controller
{
    public function index(Request $request): View
    {
        $query = Language::query()->orderBy('sort_order')->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%");
            });
        }

        $sortCols = ['name', 'code', 'is_active', 'sort_order'];
        $sort = $request->get('sort', 'name');
        $order = $request->get('order', 'asc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sort, $sortCols)) {
            $query->orderBy($sort, $order);
        } else {
            $query->orderBy('sort_order')->orderBy('name');
        }
        $languages = $query->paginate(admin_per_page(15))->withQueryString();
        return view('admin.languages.index', compact('languages'));
    }

    public function create(): View
    {
        return view('admin.languages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:languages,code|regex:/^[a-z]{2}([_-][a-z0-9]+)?$/',
            'flag' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        if (! empty($validated['flag'])) {
            $validated['flag'] = trim($validated['flag']);
        } else {
            $validated['flag'] = null;
        }
        Language::create($validated);
        return redirect()->route('admin.languages.index')->with('success', 'Language created successfully.');
    }

    public function edit(Language $language): View
    {
        return view('admin.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|regex:/^[a-z]{2}([_-][a-z0-9]+)?$/|unique:languages,code,' . $language->id,
            'flag' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['flag'] = ! empty(trim($validated['flag'] ?? '')) ? trim($validated['flag']) : null;
        $language->update($validated);
        return redirect()->route('admin.languages.index')->with('success', 'Language updated successfully.');
    }

    public function destroy(Language $language): RedirectResponse
    {
        $language->delete();
        if (
            session('app_locale') === $language->code
            || session('admin_locale') === $language->code
            || session('website_locale') === $language->code
        ) {
            session()->forget(['app_locale', 'admin_locale', 'website_locale']);
        }
        return redirect()->route('admin.languages.index')->with('success', 'Language deleted successfully.');
    }
}
