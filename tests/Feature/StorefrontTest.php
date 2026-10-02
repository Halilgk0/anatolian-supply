<?php

use App\Models\Product;

test('home page lists published products in their sort order', function () {
    Product::factory()->create(['name' => 'Kaçkar Kargo Pantolon', 'slug' => 'kackar-kargo-pantolon', 'sort_order' => 20]);
    Product::factory()->illustrated()->create(['name' => 'Toros Saha Ceketi', 'slug' => 'toros-saha-ceketi', 'sort_order' => 10]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeInOrder(['Toros Saha Ceketi', 'Kaçkar Kargo Pantolon'])
        ->assertSee(route('products.show', 'toros-saha-ceketi'))
        ->assertSee(route('products.show', 'kackar-kargo-pantolon'));
});

test('home page hides unpublished products', function () {
    Product::factory()->unpublished()->create(['name' => 'Gizli Taslak Ürün']);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Gizli Taslak Ürün');
});

test('home page links visitors to the Instagram account', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('https://www.instagram.com/anatoliansupplyco/?utm_source=ig_web_button_share_sheet', false);
});

test('product page shows details and both contact options without a checkout', function () {
    $product = Product::factory()->create([
        'name' => 'Kaçkar Kargo Pantolon',
        'code' => 'AS-204',
        'specs' => [['label' => 'Kumaş', 'value' => '%98 pamuk, %2 elastan ripstop']],
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Kaçkar Kargo Pantolon')
        ->assertSee('AS-204')
        ->assertSee('%98 pamuk, %2 elastan ripstop')
        ->assertSee('Instagram’dan sor')
        ->assertSee('https://www.instagram.com/anatoliansupplyco/?utm_source=ig_web_button_share_sheet', false)
        ->assertSee(route('products.inquiries.store', $product))
        ->assertDontSee('Sepete ekle');
});

test('product page shows the uploaded photo and colour photos', function () {
    $product = Product::factory()->create([
        'images' => [['path' => 'products/ana.jpg', 'cutout' => false]],
        'colors' => [['name' => 'Çöl deseni', 'hex' => '#c9b48e', 'image' => 'products/col.png', 'cutout' => true]],
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('src="/storage/products/ana.jpg"', false)
        ->assertSee('data-image="/storage/products/col.png"', false);
});

test('photos load on any address the site is opened with', function () {
    config(['app.url' => 'http://localhost:8000']);

    $product = Product::factory()->create(['images' => [['path' => 'products/ana.jpg', 'cutout' => false]]]);

    $this->get('http://192.168.1.105:8000/urunler/'.$product->slug)
        ->assertOk()
        ->assertSee('src="/storage/products/ana.jpg"', false)
        ->assertDontSee('localhost:8000/storage', false);
});

test('product page draws illustrated products with their colour palette', function () {
    $product = Product::factory()->illustrated('backpack')->create([
        'colors' => [['name' => 'Zeytin', 'hex' => '#5f6744', 'image' => null, 'cutout' => false]],
    ]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('--art-main: #5f6744', false)
        ->assertSee('çizimi, Zeytin renk');
});

test('product page offers a prefilled mailto link to the store inbox', function () {
    $product = Product::factory()->create(['name' => 'Erciyes 35L Sırt Çantası', 'code' => 'AS-310']);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('mailto:anatoliansupplyco@gmail.com?subject='.rawurlencode('Bilgi talebi: Erciyes 35L Sırt Çantası (AS-310)'), false)
        ->assertSee(rawurlencode('Erciyes 35L Sırt Çantası (AS-310) hakkında bilgi almak istiyorum'), false);
});

test('unpublished product page returns not found', function () {
    $product = Product::factory()->unpublished()->create();

    $this->get(route('products.show', $product))->assertNotFound();
});

test('unknown product returns not found', function () {
    $this->get('/urunler/olmayan-urun')->assertNotFound();
});

test('product with a purchase link sends visitors to the marketplace', function () {
    $product = Product::factory()->create(['purchase_url' => 'https://dolap.com/urun/ornek-ilan-123']);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('Dolap üzerinden satın al')
        ->assertSee('href="https://dolap.com/urun/ornek-ilan-123" target="_blank" rel="noopener" data-purchase-link', false)
        ->assertSee('Ödeme ve kargo Dolap üzerinden yapılır.');
});

test('product without a purchase link shows no buy button', function () {
    $product = Product::factory()->create(['purchase_url' => null]);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertDontSee('üzerinden satın al')
        ->assertDontSee('data-purchase-link', false)
        ->assertSee('Sitede satış yapılmıyor.');
});

test('the marketplace is named from the link', function (?string $link, ?string $platform) {
    expect((new Product(['purchase_url' => $link]))->purchasePlatform())->toBe($platform);
})->with([
    'dolap' => ['https://dolap.com/urun/ceket-1', 'Dolap'],
    'letgo with www' => ['https://www.letgo.com/item/xyz', 'letgo'],
    'sahibinden on mobile' => ['https://m.sahibinden.com/ilan/123', 'sahibinden.com'],
    'unknown site' => ['https://www.ornekpazar.com/ilan/9', 'ornekpazar.com'],
    'no link' => [null, null],
]);
