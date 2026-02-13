<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ProductStatus;
use App\Application\Dtos\Product\UpdateProductStatusDTO;

class UpdateProductStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(ProductStatus::class)],
        ];
    }

    public function toDto(): UpdateProductStatusDTO
    {
        return new UpdateProductStatusDTO(
            status: $this->validated('status'),
        );
    }
}