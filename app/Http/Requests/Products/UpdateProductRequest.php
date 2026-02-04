<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (count($this->all()) === 0) {
                $validator->errors()->add('fields', 'At least one field must be provided.');
            }
        });
    }
}
