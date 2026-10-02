<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * How many products hang on the home page rail; the rest are on the catalog page.
     */
    private const RAIL_LIMIT = 6;

    /**
     * Show the landing page with the product rail.
     */
    public function __invoke(): View
    {
        return view('home', [
            'products' => Product::query()->published()->ordered()->limit(self::RAIL_LIMIT)->get(),
            'totalCount' => Product::query()->published()->count(),
        ]);
    }
}
