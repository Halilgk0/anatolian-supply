<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property array<int, array{path: string, cutout: bool}> $images
 * @property array<int, array{name: string, hex: string, image: ?string, cutout: bool}> $colors
 * @property array<int, string> $sizes
 * @property array<int, string> $features
 * @property array<int, array{label: string, value: string}> $specs
 */
#[Fillable([
    'slug', 'code', 'name', 'category', 'tagline', 'description', 'illustration',
    'images', 'colors', 'sizes', 'features', 'specs', 'is_published', 'sort_order',
])]
#[RouteKey('slug')]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * SVG drawings available for products that have no photos.
     */
    public const ILLUSTRATIONS = ['jacket', 'pants', 'backpack'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'images' => 'array',
            'colors' => 'array',
            'sizes' => 'array',
            'features' => 'array',
            'specs' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Suggest the next free stock code, e.g. AS-311 after AS-310.
     */
    public static function nextCode(): string
    {
        $highest = static::query()->pluck('code')
            ->map(fn (string $code): int => preg_match('/(\d+)$/', $code, $matches) ? (int) $matches[1] : 0)
            ->max();

        return 'AS-'.max(101, ($highest ?? 100) + 1);
    }

    public function hasIllustration(): bool
    {
        return in_array($this->illustration, self::ILLUSTRATIONS, true);
    }

    /**
     * @return array{path: string, cutout: bool}|null
     */
    public function mainImage(): ?array
    {
        return $this->images[0] ?? null;
    }

    public static function mediaUrl(string $path): string
    {
        return Storage::disk(config('store.media_disk'))->url($path);
    }

    /**
     * Every stored file that belongs to this product.
     *
     * @return array<int, string>
     */
    public function mediaPaths(): array
    {
        return array_values(array_filter([
            ...array_column($this->images ?? [], 'path'),
            ...array_column($this->colors ?? [], 'image'),
        ]));
    }

    /**
     * @return array<int, string>
     */
    public function colorNames(): array
    {
        return array_column($this->colors ?? [], 'name');
    }

    /**
     * Fill, shade and detail tones for the SVG drawings, derived from one colour.
     *
     * @param  array{hex: string}  $color
     * @return array{main: string, shade: string, detail: string}
     */
    public static function palette(array $color): array
    {
        $darken = function (float $factor) use ($color): string {
            $channels = array_map('hexdec', str_split(ltrim($color['hex'], '#'), 2));

            return '#'.implode('', array_map(fn (int $channel): string => sprintf('%02x', (int) round($channel * $factor)), $channels));
        };

        return ['main' => $color['hex'], 'shade' => $darken(0.78), 'detail' => $darken(0.52)];
    }

    public function url(): string
    {
        return route('products.show', $this);
    }

    public function inquiryMessage(): string
    {
        return "Merhaba, {$this->name} ({$this->code}) hakkında bilgi almak istiyorum. Stok ve fiyat bilgisini paylaşabilir misiniz?";
    }

    public function inquirySubject(): string
    {
        return "Bilgi talebi: {$this->name} ({$this->code})";
    }

    public function inquiryBody(): string
    {
        return implode("\n", array_filter([
            $this->inquiryMessage(),
            '',
            "Ürün: {$this->name}",
            "Ürün kodu: {$this->code}",
            "Kategori: {$this->category}",
            $this->colors ? 'Renkler: '.implode(', ', $this->colorNames()) : null,
            $this->sizes ? 'Bedenler: '.implode(', ', $this->sizes) : null,
            "Bağlantı: {$this->url()}",
        ], fn (?string $line): bool => $line !== null));
    }

    /**
     * Build a mailto link that opens the visitor's mail app with the product details filled in.
     */
    public function mailtoLink(string $recipient): string
    {
        return 'mailto:'.$recipient.'?'.http_build_query([
            'subject' => $this->inquirySubject(),
            'body' => $this->inquiryBody(),
        ], '', '&', PHP_QUERY_RFC3986);
    }
}
