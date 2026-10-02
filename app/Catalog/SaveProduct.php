<?php

namespace App\Catalog;

use App\Http\Requests\SaveProductRequest;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Creates or updates a product from the admin form, including its photos.
 */
class SaveProduct
{
    public function __construct(private ProductImages $images) {}

    public function handle(Product $product, SaveProductRequest $request): Product
    {
        $previousPaths = $product->mediaPaths();

        $product->fill([
            ...$request->safe()->only(['name', 'code', 'category', 'tagline', 'description']),
            'purchase_url' => filled($request->validated('purchase_url')) ? trim($request->validated('purchase_url')) : null,
            'features' => $request->features(),
            'specs' => $request->specs(),
            'sizes' => $request->sizes(),
            'is_published' => $request->boolean('is_published'),
            'sort_order' => $request->validated('sort_order') ?? 100,
            'images' => $this->images($product, $request),
            'colors' => $this->colors($product, $request),
        ]);

        if (! $product->exists) {
            $product->slug = $this->uniqueSlug($product->name);
        }

        $product->save();

        $this->images->delete(array_diff($previousPaths, $product->mediaPaths()));

        return $product;
    }

    /**
     * Kept photos plus new uploads, with the chosen main photo moved to the front.
     *
     * @return array<int, array{path: string, cutout: bool}>
     */
    private function images(Product $product, SaveProductRequest $request): array
    {
        $removed = $request->validated('remove_images', []);

        $kept = collect($product->images ?? [])
            ->reject(fn (array $image): bool => in_array($image['path'], $removed, true))
            ->keyBy(fn (array $image): string => 'existing:'.$image['path']);

        $cutoutsFromBrowser = array_values($request->validated('image_cutouts', []));

        $added = collect($request->file('images', []))
            ->values()
            ->mapWithKeys(fn (UploadedFile $file, int $index): array => [
                "new:{$index}" => $this->images->store($file, (bool) ($cutoutsFromBrowser[$index] ?? false)),
            ]);

        $all = $kept->merge($added);
        $main = $request->validated('main_image');

        if ($main !== null && $all->has($main)) {
            $all = collect([$main => $all->get($main)])->merge($all->except($main));
        }

        return $all->values()->all();
    }

    /**
     * @return array<int, array{name: string, hex: string, image: ?string, cutout: bool}>
     */
    private function colors(Product $product, SaveProductRequest $request): array
    {
        $existingCutouts = collect($product->colors ?? [])
            ->filter(fn (array $color): bool => filled($color['image']))
            ->mapWithKeys(fn (array $color): array => [$color['image'] => $color['cutout']]);

        return collect($request->validated('colors', []))
            ->map(function (array $color, int|string $index) use ($request, $existingCutouts): array {
                $image = null;
                $cutout = false;
                $existing = $color['existing_image'] ?? null;

                if ($existing !== null && $existingCutouts->has($existing) && empty($color['remove_image'])) {
                    $image = $existing;
                    $cutout = $existingCutouts->get($existing);
                }

                if ($file = $request->file("colors.{$index}.image")) {
                    ['path' => $image, 'cutout' => $cutout] = $this->images->store($file, (bool) ($color['image_cutout'] ?? false));
                }

                return [
                    'name' => trim($color['name']),
                    'hex' => strtolower($color['hex']),
                    'image' => $image,
                    'cutout' => $cutout,
                ];
            })
            ->values()
            ->all();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name, language: 'tr') ?: 'urun';
        $slug = $base;
        $suffix = 2;

        while (Product::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
