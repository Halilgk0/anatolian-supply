<?php

use App\Mail\ProductInquiryMail;
use App\Models\Product;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();

    Product::factory()->create([
        'slug' => 'toros-saha-ceketi',
        'code' => 'AS-101',
        'name' => 'Toros Saha Ceketi',
        'colors' => [
            ['name' => 'Zeytin', 'hex' => '#5f6744', 'image' => null, 'cutout' => false],
            ['name' => 'Coyote', 'hex' => '#a08259', 'image' => null, 'cutout' => false],
        ],
        'sizes' => ['S', 'M', 'L', 'XL'],
    ]);

    Product::factory()->create([
        'slug' => 'erciyes-35l-sirt-cantasi',
        'colors' => [['name' => 'Coyote', 'hex' => '#a08259', 'image' => null, 'cutout' => false]],
        'sizes' => ['Tek beden'],
    ]);
});

function validInquiry(array $overrides = []): array
{
    return [
        'name' => 'Deniz Yılmaz',
        'email' => 'deniz@example.com',
        'phone' => '0555 000 00 00',
        'color' => 'Coyote',
        'size' => 'L',
        'message' => 'Merhaba, L beden stokta var mı?',
        ...$overrides,
    ];
}

test('inquiry is emailed to the store inbox with the product and visitor details', function () {
    $response = $this->postJson(route('products.inquiries.store', 'toros-saha-ceketi'), validInquiry());

    $response->assertOk()
        ->assertJson(['message' => 'Talebin gönderildi. deniz@example.com adresine en kısa sürede dönüş yapacağız.']);

    Mail::assertSent(ProductInquiryMail::class, function (ProductInquiryMail $mail): bool {
        return $mail->hasTo('anatoliansupplyco@gmail.com')
            && $mail->hasReplyTo('deniz@example.com', 'Deniz Yılmaz')
            && $mail->hasSubject('Bilgi talebi: Toros Saha Ceketi (AS-101)')
            && $mail->product->slug === 'toros-saha-ceketi'
            && $mail->inquiry['size'] === 'L';
    });
});

test('inquiry without javascript redirects back with a confirmation', function () {
    $productUrl = route('products.show', 'toros-saha-ceketi');

    $this->from($productUrl)
        ->post(route('products.inquiries.store', 'toros-saha-ceketi'), validInquiry())
        ->assertRedirect($productUrl)
        ->assertSessionHas('inquiry_sent');

    Mail::assertSent(ProductInquiryMail::class);
});

test('inquiry requires name, email and message', function () {
    $this->postJson(route('products.inquiries.store', 'toros-saha-ceketi'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'name' => 'Ad soyad alanını doldurun.',
            'email' => 'E-posta alanını doldurun.',
            'message' => 'Mesaj alanını doldurun.',
        ]);

    Mail::assertNothingSent();
});

test('inquiry rejects an invalid email address', function () {
    $this->postJson(route('products.inquiries.store', 'toros-saha-ceketi'), validInquiry(['email' => 'deniz']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email' => 'Geçerli bir e-posta adresi yazın, örneğin ad@ornek.com.']);

    Mail::assertNothingSent();
});

test('inquiry rejects options the product is not offered in', function (string $field, string $value) {
    $this->postJson(route('products.inquiries.store', 'erciyes-35l-sirt-cantasi'), validInquiry([$field => $value]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);

    Mail::assertNothingSent();
})->with([
    'colour from another product' => ['color', 'Kurşuni'],
    'size the bag does not come in' => ['size', 'L'],
]);

test('inquiry filled in by a bot through the hidden field is not sent', function () {
    $this->postJson(route('products.inquiries.store', 'toros-saha-ceketi'), validInquiry(['website' => 'http://spam.example']))
        ->assertUnprocessable();

    Mail::assertNothingSent();
});

test('inquiry for an unpublished product returns not found', function () {
    $product = Product::factory()->unpublished()->create();

    $this->postJson(route('products.inquiries.store', $product), validInquiry(['color' => null, 'size' => null]))->assertNotFound();

    Mail::assertNothingSent();
});

test('inquiry for an unknown product returns not found', function () {
    $this->postJson('/urunler/olmayan-urun/bilgi-talebi', validInquiry())->assertNotFound();

    Mail::assertNothingSent();
});

test('inquiry email shows the request details', function () {
    $product = Product::factory()->create(['name' => 'Kaçkar Kargo Pantolon', 'code' => 'AS-204', 'slug' => 'kackar-kargo-pantolon']);

    $mail = new ProductInquiryMail($product, validInquiry(['size' => '32', 'color' => 'Zeytin', 'phone' => null]));

    $mail->assertSeeInHtml('Kaçkar Kargo Pantolon')
        ->assertSeeInHtml('AS-204')
        ->assertSeeInHtml('Merhaba, L beden stokta var mı?')
        ->assertSeeInHtml('Zeytin')
        ->assertSeeInHtml('deniz@example.com')
        ->assertSeeInHtml('Belirtilmedi')
        ->assertSeeInHtml(route('products.show', 'kackar-kargo-pantolon'));
});

test('live site without mail settings offers the visitor mail app instead of the form', function () {
    app()['env'] = 'production';
    config(['mail.default' => 'log']);

    $this->get(route('products.show', 'toros-saha-ceketi'))
        ->assertOk()
        ->assertSee('E-posta uygulamasında aç')
        ->assertSee('data-inquiry-form method="POST"', false)
        ->assertSee('novalidate  hidden', false);

    $this->withoutMiddleware(PreventRequestForgery::class)
        ->postJson(route('products.inquiries.store', 'toros-saha-ceketi'), validInquiry())
        ->assertServiceUnavailable();

    Mail::assertNothingSent();
});

test('live site with mail settings shows the inquiry form', function () {
    app()['env'] = 'production';
    config(['mail.default' => 'smtp']);

    $this->get(route('products.show', 'toros-saha-ceketi'))
        ->assertOk()
        ->assertDontSee('E-posta uygulamasında aç')
        ->assertSee('Talebi gönder');
});
