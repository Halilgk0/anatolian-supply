<?php

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config(['store.media_disk' => 'database']);
});

test('an empty store gets the demo catalog with its photos', function () {
    $this->artisan('magaza:hazirla')
        ->expectsOutputToContain('örnek ürünler eklendi')
        ->assertSuccessful();

    expect(Product::query()->pluck('code')->sort()->values()->all())->toBe(['AS-101', 'AS-204', 'AS-310', 'AS-415']);

    $boonie = Product::query()->where('slug', 'agri-boonie-sapka')->sole();

    foreach ($boonie->mediaPaths() as $path) {
        expect(Storage::disk('database')->exists($path))->toBeTrue();
    }
});

test('a store with products is left untouched', function () {
    Product::factory()->create(['name' => 'Kendi Ürünüm']);

    $this->artisan('magaza:hazirla')
        ->expectsOutputToContain('Ürünler zaten var')
        ->assertSuccessful();

    expect(Product::query()->pluck('name')->all())->toBe(['Kendi Ürünüm']);
});
