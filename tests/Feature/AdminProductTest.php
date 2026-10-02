<?php

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

const ADMIN_KEY = 'test-yonetim-anahtari-123';

beforeEach(function () {
    config(['store.admin_key' => ADMIN_KEY, 'store.media_disk' => 'public']);
    Storage::fake('public');
});

function adminRoute(string $name, array $parameters = []): string
{
    return route($name, ['adminKey' => ADMIN_KEY, ...$parameters]);
}

/**
 * A small PNG with a see-through background and an opaque square in the middle.
 */
function cutoutPng(string $name = 'dekupe.png'): UploadedFile
{
    $image = imagecreatetruecolor(60, 60);
    imagesavealpha($image, true);
    imagealphablending($image, false);
    imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
    imagefilledrectangle($image, 20, 20, 40, 40, imagecolorallocatealpha($image, 90, 100, 60, 0));

    ob_start();
    imagepng($image);

    return UploadedFile::fake()->createWithContent($name, (string) ob_get_clean());
}

function productInput(array $overrides = []): array
{
    return [
        'name' => 'Ağrı Boonie Şapka',
        'code' => 'AS-415',
        'category' => 'Aksesuar',
        'tagline' => 'Geniş kenarlı arazi şapkası.',
        'description' => 'Güneşte ve hafif yağmurda yüzü korur.',
        'features' => "- Geniş kenar\n\n• Havalandırma delikleri\n",
        'specs' => [
            ['label' => 'Kumaş', 'value' => 'Ripstop'],
            ['label' => '', 'value' => ''],
        ],
        'sizes' => ['M', 'L'],
        'colors' => [
            ['name' => 'Orman deseni', 'hex' => '#5B5A3C'],
        ],
        'images' => [UploadedFile::fake()->image('orman.jpg', 800, 600)],
        'is_published' => '1',
        'sort_order' => '40',
        ...$overrides,
    ];
}

test('admin pages are hidden behind the secret key', function () {
    $this->get('/yonetim/yanlis-anahtar/urunler')->assertNotFound();
    $this->get('/yonetim/yanlis-anahtar')->assertNotFound();
});

test('admin is switched off when no key is configured', function () {
    config(['store.admin_key' => null]);

    $this->get('/yonetim/'.ADMIN_KEY.'/urunler')->assertNotFound();
});

test('secret link opens the product list, including drafts, without being indexed', function () {
    Product::factory()->create(['name' => 'Yayındaki Ürün']);
    Product::factory()->unpublished()->create(['name' => 'Taslak Ürün']);

    $this->get(adminRoute('admin.home'))->assertRedirect(adminRoute('admin.products.index'));

    $this->get(adminRoute('admin.products.index'))
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
        ->assertSee('Yayındaki Ürün')
        ->assertSee('Taslak Ürün')
        ->assertSee(adminRoute('admin.products.create'));
});

test('new product form suggests the next free product code', function () {
    Product::factory()->create(['code' => 'AS-310']);

    $this->get(adminRoute('admin.products.create'))
        ->assertOk()
        ->assertSee('value="AS-311"', false);
});

test('admin adds a product with photos, colours, sizes and details', function () {
    $response = $this->post(adminRoute('admin.products.store'), productInput([
        'images' => [UploadedFile::fake()->image('orman.jpg', 800, 600), cutoutPng()],
        'main_image' => 'new:1',
        'colors' => [
            ['name' => 'Orman deseni', 'hex' => '#5B5A3C'],
            ['name' => 'Çöl deseni', 'hex' => '#c9b48e', 'image' => cutoutPng('col.png')],
        ],
    ]));

    $response->assertRedirect(adminRoute('admin.products.index'))
        ->assertSessionHas('status', '“Ağrı Boonie Şapka” eklendi.');

    $product = Product::query()->sole();

    expect($product)
        ->slug->toBe('agri-boonie-sapka')
        ->code->toBe('AS-415')
        ->is_published->toBeTrue()
        ->sort_order->toBe(40)
        ->sizes->toBe(['M', 'L'])
        ->features->toBe(['Geniş kenar', 'Havalandırma delikleri'])
        ->specs->toBe([['label' => 'Kumaş', 'value' => 'Ripstop']]);

    expect($product->images)->toHaveCount(2)
        ->and($product->images[0]['cutout'])->toBeTrue()
        ->and($product->images[1]['cutout'])->toBeFalse()
        ->and($product->colors[0])->toMatchArray(['name' => 'Orman deseni', 'hex' => '#5b5a3c', 'image' => null])
        ->and($product->colors[1]['cutout'])->toBeTrue();

    foreach ($product->mediaPaths() as $path) {
        Storage::disk('public')->assertExists($path);
    }
});

test('admin cannot add a product without a photo', function () {
    $this->from(adminRoute('admin.products.create'))
        ->post(adminRoute('admin.products.store'), productInput(['images' => []]))
        ->assertRedirect(adminRoute('admin.products.create'))
        ->assertSessionHasErrors(['images' => 'En az bir ürün görseli ekle.']);

    expect(Product::query()->count())->toBe(0);
});

test('admin sees which required fields are missing', function () {
    $this->post(adminRoute('admin.products.store'), productInput(['name' => '', 'tagline' => '', 'category' => '']))
        ->assertSessionHasErrors([
            'name' => 'Ürün adı alanını doldur.',
            'tagline' => 'Kısa açıklama alanını doldur.',
            'category' => 'Kategori alanını doldur.',
        ]);
});

test('admin cannot reuse another product code', function () {
    Product::factory()->create(['code' => 'AS-415']);

    $this->post(adminRoute('admin.products.store'), productInput())
        ->assertSessionHasErrors(['code' => 'Bu ürün kodu başka bir üründe kullanılıyor.']);
});

test('admin rejects files that are not photos', function () {
    $this->post(adminRoute('admin.products.store'), productInput([
        'images' => [UploadedFile::fake()->create('katalog.pdf', 100, 'application/pdf')],
    ]))->assertSessionHasErrors(['images.0' => 'Yalnızca JPEG, PNG veya WEBP görsel yükleyebilirsin.']);
});

test('a second product with the same name gets its own link', function () {
    Product::factory()->create(['slug' => 'agri-boonie-sapka', 'code' => 'AS-900']);

    $this->post(adminRoute('admin.products.store'), productInput())->assertSessionHasNoErrors();

    expect(Product::query()->where('code', 'AS-415')->value('slug'))->toBe('agri-boonie-sapka-2');
});

test('admin updates a product, removing a photo and keeping its link', function () {
    Storage::disk('public')->put('products/eski.jpg', 'x');
    Storage::disk('public')->put('products/kalan.jpg', 'x');

    $product = Product::factory()->create([
        'slug' => 'agri-boonie-sapka',
        'code' => 'AS-415',
        'images' => [
            ['path' => 'products/eski.jpg', 'cutout' => false],
            ['path' => 'products/kalan.jpg', 'cutout' => false],
        ],
    ]);

    $this->put(adminRoute('admin.products.update', ['product' => $product]), productInput([
        'name' => 'Ağrı Boonie Şapka V2',
        'images' => [],
        'remove_images' => ['products/eski.jpg'],
        'is_published' => '0',
    ]))->assertRedirect(adminRoute('admin.products.index'));

    $product->refresh();

    expect($product)
        ->name->toBe('Ağrı Boonie Şapka V2')
        ->slug->toBe('agri-boonie-sapka')
        ->is_published->toBeFalse()
        ->images->toBe([['path' => 'products/kalan.jpg', 'cutout' => false]]);

    Storage::disk('public')->assertMissing('products/eski.jpg');
    Storage::disk('public')->assertExists('products/kalan.jpg');
});

test('admin keeps, replaces or removes colour photos', function () {
    Storage::disk('public')->put('products/orman.jpg', 'x');
    Storage::disk('public')->put('products/col.jpg', 'x');

    $product = Product::factory()->create([
        'code' => 'AS-415',
        'colors' => [
            ['name' => 'Orman deseni', 'hex' => '#5b5a3c', 'image' => 'products/orman.jpg', 'cutout' => false],
            ['name' => 'Çöl deseni', 'hex' => '#c9b48e', 'image' => 'products/col.jpg', 'cutout' => false],
        ],
    ]);

    $this->put(adminRoute('admin.products.update', ['product' => $product]), productInput([
        'images' => [],
        'colors' => [
            ['name' => 'Orman deseni', 'hex' => '#5b5a3c', 'existing_image' => 'products/orman.jpg'],
            ['name' => 'Çöl deseni', 'hex' => '#c9b48e', 'existing_image' => 'products/col.jpg', 'remove_image' => '1'],
            ['name' => 'Siyah', 'hex' => '#3a3b35', 'existing_image' => 'products/baskasinin-dosyasi.jpg'],
        ],
    ]))->assertSessionHasNoErrors();

    $colors = $product->refresh()->colors;

    expect(array_column($colors, 'image'))->toBe(['products/orman.jpg', null, null]);

    Storage::disk('public')->assertExists('products/orman.jpg');
    Storage::disk('public')->assertMissing('products/col.jpg');
});

test('illustrated products can be saved without uploading a photo', function () {
    $product = Product::factory()->illustrated('jacket')->create(['code' => 'AS-415']);

    $this->put(adminRoute('admin.products.update', ['product' => $product]), productInput(['images' => []]))
        ->assertSessionHasNoErrors();
});

test('admin deletes a product together with its photos', function () {
    Storage::disk('public')->put('products/ana.jpg', 'x');
    Storage::disk('public')->put('products/renk.jpg', 'x');

    $product = Product::factory()->create([
        'images' => [['path' => 'products/ana.jpg', 'cutout' => false]],
        'colors' => [['name' => 'Zeytin', 'hex' => '#5f6744', 'image' => 'products/renk.jpg', 'cutout' => false]],
    ]);

    $this->delete(adminRoute('admin.products.destroy', ['product' => $product]))
        ->assertRedirect(adminRoute('admin.products.index'));

    $this->assertModelMissing($product);
    Storage::disk('public')->assertMissing(['products/ana.jpg', 'products/renk.jpg']);
});
