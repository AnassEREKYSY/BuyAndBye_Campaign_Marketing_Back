<?php

declare(strict_types=1);

namespace Src\Api\V1\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Src\Infrastructure\Services\Base64ImageConverter;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $productId = $this->route('productId');

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'category_id' => ['sometimes', 'nullable', 'uuid', 'exists:categories,id'],
            'condition' => ['sometimes', 'string', 'in:New,LikeNew,VeryGood,Good,Acceptable'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
            'stockQuantity' => ['sometimes', 'integer', 'min:0'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['image', 'max:5120'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'weight_kg' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'sku' => ['sometimes', 'nullable', 'string', 'max:100', 'unique:products,sku,' . $productId],
            'is_digital' => ['sometimes', 'boolean'],
            'allow_returns' => ['sometimes', 'boolean'],
            'return_days' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $converter = app(Base64ImageConverter::class);
        $images = null;

        if ($this->file('images')) {
            $images = $converter->toDataUriList($this->file('images'));
        }

        $this->merge([
            'images_data' => $images,
            'stock_quantity' => $this->input('stock_quantity', $this->input('stockQuantity')),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $payload = $this->except(['images', 'images_data']);
            if (count($payload) === 0 && $this->images_data === null) {
                $validator->errors()->add('fields', 'At least one field must be provided.');
            }
        });
    }
}
