<x-mail::message>
# {{ $product->name }} için bilgi talebi

**{{ $inquiry['name'] }}** web sitesindeki ürün sayfasından bilgi istedi. Bu e-postayı yanıtladığınızda cevabınız doğrudan {{ $inquiry['email'] }} adresine gider.

<x-mail::panel>
{{ $inquiry['message'] }}
</x-mail::panel>

<x-mail::table>
| Talep | |
| :-- | :-- |
| Ürün | {{ $product->name }} |
| Ürün kodu | {{ $product->code }} |
| Kategori | {{ $product->category }} |
| Seçilen renk | {{ $inquiry['color'] ?? 'Belirtilmedi' }} |
| Seçilen beden | {{ $inquiry['size'] ?? 'Belirtilmedi' }} |
| Ad soyad | {{ $inquiry['name'] }} |
| E-posta | {{ $inquiry['email'] }} |
| Telefon | {{ $inquiry['phone'] ?? 'Belirtilmedi' }} |
</x-mail::table>

<x-mail::button :url="$product->url()">
Ürün sayfasını aç
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
