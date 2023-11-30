<?php

namespace App\Http\Controllers;

use Random\Randomizer;

class ArticleController extends Controller
{
    public function __invoke()
    {
        $randomizer = new Randomizer();
        dd($randomizer->getInt(1, 920000));
    }
}
