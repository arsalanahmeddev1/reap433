<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class BiblicalTriviaController extends Controller
{
    public function index(): View
    {
        return view('screens.web.biblical-trivia.index');
    }
}
