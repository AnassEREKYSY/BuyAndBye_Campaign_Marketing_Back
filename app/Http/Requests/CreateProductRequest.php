<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Enum;
use App\Enums\ProductCondition; 
use Illuminate\Foundation\Http\FormRequest;


class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_digital' => filter_var($this->is_digital, FILTER_VALIDATE_BOOLEAN),
            'allow_returns' => filter_var($this->allow_returns, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'condition' => ['required', new Enum(ProductCondition::class)],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'tags' => ['nullable', 'array'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'is_digital' => ['required', 'boolean'],
            'allow_returns' => ['required', 'boolean'],
            'return_days' => ['required', 'integer', 'min:0'],
        ];
    }
}