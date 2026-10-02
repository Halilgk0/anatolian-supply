<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('photos stored on the database disk are served with long-lived cache headers', function () {
    $photo = UploadedFile::fake()->image('ceket.jpg', 120, 80);

    $path = Storage::disk('database')->putFile('products', $photo);

    expect($path)->toStartWith('products/')
        ->and(Storage::disk('database')->exists($path))->toBeTrue()
        ->and(Storage::disk('database')->url($path))->toBe('/media/'.$path);

    $response = $this->get('/media/'.$path)
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg')
        ->assertHeader('Cache-Control', 'immutable, max-age=31536000, public, s-maxage=31536000');

    expect($response->getContent())->toBe($photo->getContent())
        ->and($response->headers->getCookies())->toBeEmpty();
});

test('deleted photos are no longer served', function () {
    Storage::disk('database')->put('products/silinecek.png', 'png-bytes');
    Storage::disk('database')->delete('products/silinecek.png');

    expect(Storage::disk('database')->exists('products/silinecek.png'))->toBeFalse();

    $this->get('/media/products/silinecek.png')->assertNotFound();
});

test('database disk reports file details and lists a folder', function () {
    Storage::disk('database')->put('products/a.txt', 'abc');
    Storage::disk('database')->put('products/alt/b.txt', 'de');
    Storage::disk('database')->put('diger/c.txt', 'f');

    expect(Storage::disk('database')->get('products/a.txt'))->toBe('abc')
        ->and(Storage::disk('database')->size('products/a.txt'))->toBe(3)
        ->and(Storage::disk('database')->files('products'))->toBe(['products/a.txt'])
        ->and(Storage::disk('database')->allFiles('products'))->toBe(['products/a.txt', 'products/alt/b.txt']);
});
