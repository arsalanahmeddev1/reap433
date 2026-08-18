<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sitemap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SitemapController extends Controller
{
    public function index(): View
    {
        $sitemap = Sitemap::current();

        return view('screens.admin.sitemaps.index', compact('sitemap'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['nullable', 'string'],
        ]);

        $content = $this->normalizedContent($validated['content'] ?? '');

        if ($content !== null && ! $this->looksLikeSitemapXml($content)) {
            return redirect()
                ->route('sitemaps.index')
                ->withInput()
                ->withErrors([
                    'content' => __('Paste a complete sitemap XML (urlset or sitemapindex).'),
                ]);
        }

        Sitemap::current()->update([
            'content' => $content,
        ]);

        return redirect()
            ->route('sitemaps.index')
            ->with('success', __('Sitemap saved. It is live at :url', [
                'url' => url('/sitemap.xml'),
            ]));
    }

    private function normalizedContent(string $content): ?string
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content) ?? $content;
        $content = trim($content);

        return $content !== '' ? $content : null;
    }

    private function looksLikeSitemapXml(string $content): bool
    {
        if (! str_contains($content, '<')) {
            return false;
        }

        $lower = strtolower($content);

        return str_contains($lower, '<urlset')
            || str_contains($lower, '<sitemapindex')
            || str_contains($lower, '<url>');
    }
}
