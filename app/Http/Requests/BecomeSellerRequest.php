<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Profile\BecomeSellerDTO;
use Illuminate\Foundation\Http\FormRequest;

class BecomeSellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:10'],
        ];
    }

    public function toDto(): BecomeSellerDTO
    {
        return new BecomeSellerDTO(
            storeName: $this->store_name,
            countryCode: $this->country_code,
        );
    }
}
