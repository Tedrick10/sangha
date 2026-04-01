<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Website::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }
        if ($request->filled('is_published')) {
            $query->where('is_published', $request->is_published === '1');
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $sortCols = ['title', 'slug', 'type', 'is_published', 'sort_order'];
        $sort = $request->get('sort', 'sort_order');
        $order = $request->get('order', 'asc') === 'asc' ? 'asc' : 'desc';
        if (in_array($sort, $sortCols)) {
            $query->orderBy($sort, $order);
        } else {
            $query->orderBy('sort_order')->orderBy('title');
        }
        $websites = $query->paginate(admin_per_page(10))->withQueryString();
        $types = Website::distinct()->pluck('type')->filter()->values();
        return view('admin.websites.index', compact('websites', 'types'));
    }

    public function edit(Website $website): View
    {
        return view('admin.websites.edit', compact('website'));
    }

    public function update(Request $request, Website $website): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:websites,slug,' . $website->id,
            'content' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $validated['slug'] = $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['title'] ?? '');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $website->update($validated);
        return redirect()->route('admin.websites.index')->with('success', 'Website content updated successfully.');
    }
}
