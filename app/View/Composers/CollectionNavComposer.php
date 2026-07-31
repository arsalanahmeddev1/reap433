<?php

namespace App\View\Composers;

use App\Models\CollectionPage;
use Illuminate\View\View;

class CollectionNavComposer
{
    public function compose(View $view): void
    {
        $view->with(
            'collectionNavPages',
            CollectionPage::query()
                ->orderBy('title')
                ->get(['id', 'title', 'slug'])
        );
    }
}
