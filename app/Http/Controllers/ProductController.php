<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * List every published product, optionally narrowed to one category (?kategori=dis-giyim).
     */
    public function index(Request $request): View
    {
        $products = Product::query()->published()->ordered()->get();

        /** @var Collection<int, array{name: string, slug: string, count: int}> $categories */
        $categories = $products->groupBy('category')
            ->map(fn (Collection $group, string $name): array => [
                'name' => $name,
                'slug' => Str::slug($name, language: 'tr'),
                'count' => $group->count(),
            ])
            ->values();

        $activeCategory = $categories->firstWhere('slug', $request->query('kategori'));

        return view('products.index', [
            'products' => $activeCategory ? $products->where('category', $activeCategory['name'])->values() : $products,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'totalCount' => $products->count(),
        ]);
    }

    /**
     * Show a single product with its Instagram and email inquiry options.
     */
    public function show(Product $product): View
    {
        abort_unless($product->is_published, 404);

        return view('products.show', [
            'product' => $product,
            'otherProducts' => Product::query()->published()->ordered()->whereKeyNot($product->getKey())->limit(3)->get(),
        ]);
    }
}
