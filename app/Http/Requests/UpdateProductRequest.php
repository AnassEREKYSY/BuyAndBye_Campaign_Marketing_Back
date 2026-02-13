<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Product\UpdateProductDTO;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ProductCondition;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('is_digital')) {
            $merge['is_digital'] = filter_var(
                $this->is_digital,
                FILTER_VALIDATE_BOOLEAN
            );
        }

        if ($this->has('allow_returns')) {
            $merge['allow_returns'] = filter_var(
                $this->allow_returns,
                FILTER_VALIDATE_BOOLEAN
            );
        }

        $this->merge($merge);
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'category_id' => ['sometimes', 'nullable', 'uuid', 'exists:categories,id'],
            'condition' => ['sometimes', new Enum(ProductCondition::class)],
            'images' => ['sometimes', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock_quantity' => ['sometimes', 'integer', 'min:0'],
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

    public function toDto(): UpdateProductDTO
    {
        $validated = $this->validated();

        return new UpdateProductDTO(
            title: $validated['title'] ?? null,
            description: $validated['description'] ?? null,
            categoryId: $validated['category_id'] ?? null,
            condition: $validated['condition'] ?? null,
            price: isset($validated['price']) ? (float) $validated['price'] : null,
            stockQuantity: isset($validated['stock_quantity']) ? (int) $validated['stock_quantity'] : null,
            images: $this->file('images'),
            tags: $validated['tags'] ?? null,
            weightKg: isset($validated['weight_kg']) ? (float) $validated['weight_kg'] : null,
            sku: $validated['sku'] ?? null,
            isDigital: array_key_exists('is_digital', $validated)
                ? (bool) $validated['is_digital']
                : null,
            allowReturns: array_key_exists('allow_returns', $validated)
                ? (bool) $validated['allow_returns']
                : null,
            returnDays: isset($validated['return_days'])
                ? (int) $validated['return_days']
                : null,
        );
    }
}
