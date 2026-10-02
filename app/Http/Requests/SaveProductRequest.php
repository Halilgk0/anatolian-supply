<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveProductRequest extends FormRequest
{
    /**
     * Access is checked by the secret admin link middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'];

        return [
            'name' => ['required', 'string', 'max:80'],
            'code' => ['required', 'string', 'max:20', Rule::unique('products', 'code')->ignore($this->product())],
            'category' => ['required', 'string', 'max:40'],
            'tagline' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:3000'],
            'features' => ['nullable', 'string', 'max:3000'],
            'specs' => ['array', 'max:15'],
            'specs.*.label' => ['nullable', 'string', 'max:40', 'required_with:specs.*.value'],
            'specs.*.value' => ['nullable', 'string', 'max:160', 'required_with:specs.*.label'],
            'sizes' => ['array', 'max:30'],
            'sizes.*' => ['string', 'max:20', 'distinct'],
            'colors' => ['array', 'max:10'],
            'colors.*.name' => ['required', 'string', 'max:30', 'distinct:ignore_case'],
            'colors.*.hex' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'colors.*.image' => ['nullable', ...$image],
            'colors.*.existing_image' => ['nullable', 'string'],
            'colors.*.remove_image' => ['nullable', 'boolean'],
            'colors.*.image_cutout' => ['nullable', 'boolean'],
            'images' => ['array', 'max:8'],
            'images.*' => $image,
            'image_cutouts' => ['array'],
            'image_cutouts.*' => ['boolean'],
            'remove_images' => ['array'],
            'remove_images.*' => ['string'],
            'main_image' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * Every product needs something to show: a photo, or one of the built-in drawings.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $product = $this->product();
                $removed = (array) $this->input('remove_images', []);

                $keptPhotos = collect($product?->images ?? [])
                    ->reject(fn (array $image): bool => in_array($image['path'], $removed, true))
                    ->count();

                if ($keptPhotos + count($this->file('images', [])) === 0 && ! $product?->hasIllustration()) {
                    $validator->errors()->add('images', 'En az bir ürün görseli ekle.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'ürün adı',
            'code' => 'ürün kodu',
            'category' => 'kategori',
            'tagline' => 'kısa açıklama',
            'description' => 'detaylı açıklama',
            'features' => 'öne çıkanlar',
            'specs.*.label' => 'bilgi adı',
            'specs.*.value' => 'bilgi',
            'sizes.*' => 'beden',
            'colors.*.name' => 'renk adı',
            'colors.*.hex' => 'renk kodu',
            'colors.*.image' => 'renk fotoğrafı',
            'images.*' => 'görsel',
            'sort_order' => 'sıra',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':Attribute alanını doldur.',
            'required_with' => ':Attribute alanını doldur ya da satırı sil.',
            'max' => [
                'string' => ':Attribute en fazla :max karakter olabilir.',
                'file' => 'Her görsel en fazla 8 MB olabilir.',
                'numeric' => ':Attribute en fazla :max olabilir.',
                'array' => 'En fazla :max :attribute ekleyebilirsin.',
            ],
            'images.max' => 'En fazla :max görsel ekleyebilirsin.',
            'colors.max' => 'En fazla :max renk ekleyebilirsin.',
            'image' => 'Yalnızca JPEG, PNG veya WEBP görsel yükleyebilirsin.',
            'mimes' => 'Yalnızca JPEG, PNG veya WEBP görsel yükleyebilirsin.',
            'uploaded' => 'Görsel yüklenemedi; dosya çok büyük olabilir. Daha küçük bir görsel dene.',
            'distinct' => 'Aynı :attribute iki kez eklenmiş.',
            'code.unique' => 'Bu ürün kodu başka bir üründe kullanılıyor.',
            'colors.*.hex.regex' => 'Renk kodu #5f6744 biçiminde olmalı.',
            'integer' => ':Attribute bir sayı olmalı.',
            'min' => ['numeric' => ':Attribute en az :min olabilir.'],
        ];
    }

    /**
     * Feature lines from the textarea, without empty lines or typed bullet marks.
     *
     * @return array<int, string>
     */
    public function features(): array
    {
        return collect(preg_split('/\R/u', (string) $this->validated('features')))
            ->map(fn (string $line): string => trim((string) preg_replace('/^[\s\-–•*]+/u', '', $line)))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function specs(): array
    {
        return collect($this->validated('specs', []))
            ->filter(fn (array $row): bool => filled($row['label'] ?? null) && filled($row['value'] ?? null))
            ->map(fn (array $row): array => ['label' => trim($row['label']), 'value' => trim($row['value'])])
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function sizes(): array
    {
        return collect($this->validated('sizes', []))
            ->map(fn (string $size): string => trim($size))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function product(): ?Product
    {
        $product = $this->route('product');

        return $product instanceof Product ? $product : null;
    }
}
