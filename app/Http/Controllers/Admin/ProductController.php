<?php

namespace App\Http\Controllers\Admin;

use App\Catalog\ProductImages;
use App\Catalog\SaveProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveProductRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * List every product, published or not.
     */
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::query()->ordered()->get(),
        ]);
    }

    /**
     * Show the form for adding a product.
     */
    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product([
                'code' => Product::nextCode(),
                'images' => [],
                'colors' => [],
                'sizes' => [],
                'features' => [],
                'specs' => [],
                'is_published' => true,
                'sort_order' => 100,
            ]),
            'categories' => $this->categories(),
        ]);
    }

    /**
     * Save a new product.
     */
    public function store(SaveProductRequest $request, SaveProduct $saveProduct): RedirectResponse
    {
        $product = $saveProduct->handle(new Product, $request);

        return to_route('admin.products.index')->with('status', "“{$product->name}” eklendi.");
    }

    /**
     * Show the form for editing a product.
     */
    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->categories(),
        ]);
    }

    /**
     * Save changes to a product.
     */
    public function update(SaveProductRequest $request, Product $product, SaveProduct $saveProduct): RedirectResponse
    {
        $saveProduct->handle($product, $request);

        return to_route('admin.products.index')->with('status', "“{$product->name}” güncellendi.");
    }

    /**
     * Delete a product together with its photos.
     */
    public function destroy(Product $product, ProductImages $images): RedirectResponse
    {
        $paths = $product->mediaPaths();

        $product->delete();
        $images->delete($paths);

        return to_route('admin.products.index')->with('status', "“{$product->name}” silindi.");
    }

    /**
     * @return array<int, string>
     */
    private function categories(): array
    {
        return Product::query()->distinct()->orderBy('category')->pluck('category')->all();
    }
}
