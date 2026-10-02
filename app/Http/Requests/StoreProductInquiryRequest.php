<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductInquiryRequest extends FormRequest
{
    /**
     * Unpublished products behave as if they do not exist.
     */
    public function authorize(): bool
    {
        /** @var Product $product */
        $product = $this->route('product');

        abort_unless($product->is_published, 404);

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Product $product */
        $product = $this->route('product');

        return [
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'color' => ['nullable', 'string', Rule::in($product->colorNames())],
            'size' => ['nullable', 'string', Rule::in($product->sizes)],
            'message' => ['required', 'string', 'max:1000'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'ad soyad',
            'email' => 'e-posta',
            'phone' => 'telefon',
            'color' => 'renk',
            'size' => 'beden',
            'message' => 'mesaj',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':Attribute alanını doldurun.',
            'email' => 'Geçerli bir e-posta adresi yazın, örneğin ad@ornek.com.',
            'max' => ':Attribute en fazla :max karakter olabilir.',
            'in' => 'Listeden bir :attribute seçin.',
            'website.prohibited' => 'Talep gönderilemedi.',
        ];
    }
}
