<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:50'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'locale' => ['nullable', 'string', 'max:10'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'buyer_categories' => ['nullable', 'array'],
            'buyer_interests' => ['nullable', 'array'],
            'payment_methods' => ['nullable', 'array'],
            'store_name' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'vat_number' => ['nullable', 'string', 'max:100'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'support_phone' => ['nullable', 'string', 'max:50'],
            'category_tags' => ['nullable', 'array'],
            'store_description' => ['nullable', 'string'],
            'store_banner' => ['nullable', 'image', 'max:5120'],
        ];
    }

}
