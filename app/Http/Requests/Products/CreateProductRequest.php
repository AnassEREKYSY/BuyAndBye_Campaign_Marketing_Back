<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Src\Infrastructure\Services\Base64ImageConverter;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'condition' => ['required', 'string', 'in:New,LikeNew,VeryGood,Good,Acceptable'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:5120'],
            'tags' => ['nullable', 'array'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'is_digital' => ['required', 'boolean'],
            'allow_returns' => ['required', 'boolean'],
            'return_days' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Prepare input data for validation and UseCases.
     * Converts image files to base64 data URIs if present.
     */
    protected function prepareForValidation(): void
    {
        $converter = app(Base64ImageConverter::class);
        $images = null;

        if ($this->file('images')) {
            $images = $converter->toDataUriList($this->file('images'));
        }

        $this->merge([
            'images_data' => $images,
        ]);
    }
}
