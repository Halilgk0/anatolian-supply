<?php

use App\Models\Product;

test('catalog lists every published product in sort order', function () {
    Product::factory()->create(['name' => 'Kaçkar Kargo Pantolon', 'sort_order' => 20]);
    Product::factory()->create(['name' => 'Toros Saha Ceketi', 'sort_order' => 10]);
    Product::factory()->unpublished()->create(['name' => 'Gizli Taslak Ürün']);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSeeInOrder(['Toros Saha Ceketi', 'Kaçkar Kargo Pantolon'])
        ->assertDontSee('Gizli Taslak Ürün')
        ->assertSee('2 ürün.');
});

test('catalog can be narrowed to one category', function () {
    Product::factory()->create(['name' => 'Toros Saha Ceketi', 'category' => 'Dış giyim']);
    Product::factory()->create(['name' => 'Ağrı Boonie Şapka', 'category' => 'Aksesuar']);

    $response = $this->get(route('products.index', ['kategori' => 'dis-giyim']))
        ->assertOk()
        ->assertSee('Toros Saha Ceketi')
        ->assertDontSee('Ağrı Boonie Şapka')
        ->assertSee('<title>Dış giyim | Anatolian Supply Co.</title>', false);

    expect($response->getContent())->toMatch('#href="[^"]*kategori=dis-giyim"\s+aria-current="page"#');
});

test('catalog shows everything for an unknown category', function () {
    Product::factory()->create(['name' => 'Toros Saha Ceketi', 'category' => 'Dış giyim']);
    Product::factory()->create(['name' => 'Ağrı Boonie Şapka', 'category' => 'Aksesuar']);

    $this->get(route('products.index', ['kategori' => 'olmayan-kategori']))
        ->assertOk()
        ->assertSee('Toros Saha Ceketi')
        ->assertSee('Ağrı Boonie Şapka');
});

test('empty catalog points visitors to Instagram', function () {
    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Yakında')
        ->assertSee(config('store.instagram_url'), false);
});

test('site menu links to the catalog and marks it on product pages', function () {
    $product = Product::factory()->create();

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('href="'.route('products.index').'"', false);

    $productPage = $this->get(route('products.show', $product))->assertOk();

    expect($productPage->getContent())->toMatch('#href="'.preg_quote(route('products.index'), '#').'"\s+aria-current="page"#');
});

test('home rail shows the first six products and links to the full catalog', function () {
    foreach (range(1, 8) as $position) {
        Product::factory()->create(['name' => "Raf Ürünü {$position}", 'sort_order' => $position]);
    }

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Raf Ürünü 6')
        ->assertDontSee('Raf Ürünü 7')
        ->assertSee('8 ürünün hepsi tek sayfada.')
        ->assertSee('Tüm ürünleri gör');
});
