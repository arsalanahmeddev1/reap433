<?php

namespace App\Http\Controllers;

use App\Models\Sitemap;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function show(): Response
    {
        $xml = Sitemap::current()->xmlContent() ?? $this->emptySitemap();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    private function emptySitemap(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
</urlset>
XML;
    }
}
