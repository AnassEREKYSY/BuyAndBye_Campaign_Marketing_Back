<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Product\CreateProductDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'landing_url' => ['nullable', 'url', 'max:2048'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string', 'max:2048'],
        ];
    }

    public function toDto(): CreateProductDTO
    {
        return new CreateProductDTO(
            name: $this->name,
            description: $this->description,
            price: $this->price !== null ? (float) $this->price : null,
            currency: $this->currency,
            landingUrl: $this->landing_url,
            images: $this->images,
        );
    }
}