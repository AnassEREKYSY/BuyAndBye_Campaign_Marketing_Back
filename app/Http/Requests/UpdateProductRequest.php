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
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'nullable', 'string', 'max:10'],
            'landing_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'status' => ['sometimes', 'nullable', 'in:draft,active,archived'],
            'images' => ['sometimes', 'nullable', 'array'],
            'images.*' => ['string', 'max:2048'],
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
        return new UpdateProductDTO(
            name: $this->input('name'),
            description: $this->input('description'),
            price: $this->input('price') !== null ? (float) $this->input('price') : null,
            currency: $this->input('currency'),
            landingUrl: $this->input('landing_url'),
            images: $this->input('images'),
            status: $this->input('status'),
        );
    }
}