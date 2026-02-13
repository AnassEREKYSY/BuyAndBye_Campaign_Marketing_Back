<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Product\UpdateProductDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'category_id' => ['sometimes', 'nullable', 'uuid'],
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
            $payload = $this->except(['images', 'images_data']);
            if (count($payload) === 0 && $this->images_data === null) {
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
            images: $validated['images_data'] ?? null,
            tags: $validated['tags'] ?? null,
            weightKg: isset($validated['weight_kg']) ? (float) $validated['weight_kg'] : null,
            sku: $validated['sku'] ?? null,
            isDigital: $validated['is_digital'] ?? null,
            allowReturns: $validated['allow_returns'] ?? null,
            returnDays: isset($validated['return_days']) ? (int) $validated['return_days'] : null,
        );
    }
}
