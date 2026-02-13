<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Product\CreateProductDTO;
use Illuminate\Foundation\Http\FormRequest;

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
            'category_id' => ['nullable', 'uuid'],
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

    public function toDto(): CreateProductDTO
    {
        $validated = $this->validated();

        return new CreateProductDTO(
            sellerId: auth()->id(),
            title: $validated['title'],
            description: $validated['description'] ?? null,
            categoryId: $validated['category_id'] ?? null,
            condition: $validated['condition'],
            price: (float) $validated['price'],
            stockQuantity: (int) $validated['stock_quantity'],
            images: $validated['images_data'] ?? null,
            tags: $validated['tags'] ?? null,
            weightKg: isset($validated['weight_kg']) ? (float) $validated['weight_kg'] : null,
            sku: $validated['sku'] ?? null,
            isDigital: (bool) $validated['is_digital'],
            allowReturns: (bool) $validated['allow_returns'],
            returnDays: (int) $validated['return_days'],
        );
    }
}
