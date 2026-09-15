<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SitePageController extends Controller
{
    public function index(): View
    {
        $pages = SitePage::query()
            ->orderBy('title')
            ->get();

        return view('screens.admin.site-pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('screens.admin.site-pages.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ], [
            'image_url.max' => __('Site page image upload max size is 2MB.'),
        ]);

        $imagePath = null;
        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('site-pages', 'public');
        }

        SitePage::create([
            'title' => $validated['title'],
            'slug' => SitePage::slugFromTitle($validated['title']),
            'image_url' => $imagePath,
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'status' => $validated['status'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Site page created.'),
                'redirect' => route('site-pages.index'),
            ]);
        }

        return redirect()->route('site-pages.index')->with('success', __('Site page created.'));
    }

    public function edit(SitePage $sitePage): View
    {
        return view('screens.admin.site-pages.edit', ['page' => $sitePage]);
    }

    public function update(Request $request, SitePage $sitePage): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
            'remove_image' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ], [
            'image_url.max' => __('Site page image upload max size is 2MB.'),
        ]);

        $imagePath = $sitePage->image_url;

        if ($request->boolean('remove_image') && $sitePage->image_url) {
            if (! preg_match('#^https?://#i', (string) $sitePage->image_url)) {
                Storage::disk('public')->delete($sitePage->image_url);
            }
            $imagePath = null;
        }

        if ($request->hasFile('image_url')) {
            if ($sitePage->image_url && ! preg_match('#^https?://#i', (string) $sitePage->image_url)) {
                Storage::disk('public')->delete($sitePage->image_url);
            }
            $imagePath = $request->file('image_url')->store('site-pages', 'public');
        }

        $sitePage->update([
            'title' => $validated['title'],
            'slug' => SitePage::slugFromTitle($validated['title'], $sitePage->id),
            'image_url' => $imagePath,
            'description' => $validated['description'] ?? null,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'status' => $validated['status'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Site page updated.'),
                'redirect' => route('site-pages.index'),
            ]);
        }

        return redirect()->route('site-pages.index')->with('success', __('Site page updated.'));
    }

    public function destroy(Request $request, SitePage $sitePage): RedirectResponse|JsonResponse
    {
        if ($sitePage->image_url && ! preg_match('#^https?://#i', (string) $sitePage->image_url)) {
            Storage::disk('public')->delete($sitePage->image_url);
        }

        $sitePage->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Site page deleted.'),
                'redirect' => route('site-pages.index'),
            ]);
        }

        return redirect()->route('site-pages.index')->with('success', __('Site page deleted.'));
    }
}
