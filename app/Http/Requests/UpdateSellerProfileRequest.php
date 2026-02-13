<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Profile\UpdateSellerProfileDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateSellerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'company_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'vat_number' => ['sometimes', 'nullable', 'string', 'max:100'],
            'support_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'support_phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'category_tags' => ['sometimes', 'nullable', 'array'],
            'store_description' => ['sometimes', 'nullable', 'string'],
            'store_banner' => ['sometimes', 'nullable', 'image', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hasAnyField =
                $this->hasAny([
                    'store_name',
                    'company_name',
                    'vat_number',
                    'support_email',
                    'support_phone',
                    'category_tags',
                    'store_description',
                ]) || $this->hasFile('store_banner');

            if (! $hasAnyField) {
                $validator->errors()->add('fields', 'At least one field must be provided.');
            }
        });
    }

    public function toDto(): UpdateSellerProfileDTO
    {
        return new UpdateSellerProfileDTO(
            storeName: $this->store_name,
            companyName: $this->company_name,
            vatNumber: $this->vat_number,
            supportEmail: $this->support_email,
            supportPhone: $this->support_phone,
            categoryTags: $this->category_tags,
            storeDescription: $this->store_description,
            storeBanner: $this->file('store_banner'),
        );
    }
}
