<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\Base64ImageConverter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'display_name' => ['sometimes', 'string', 'max:255'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'birth_date' => ['sometimes', 'nullable', 'date'],
            'gender' => ['sometimes', 'nullable', 'string', 'max:50'],
            'country_code' => ['sometimes', 'nullable', 'string', 'max:10'],
            'locale' => ['sometimes', 'nullable', 'string', 'max:10'],
            'photo' => ['sometimes', 'nullable', 'image', 'max:5120'],
            'buyer_categories' => ['sometimes', 'nullable', 'array'],
            'buyer_interests' => ['sometimes', 'nullable', 'array'],
            'payment_methods' => ['sometimes', 'nullable', 'array'],
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

    protected function prepareForValidation(): void
    {
        $converter = app(Base64ImageConverter::class);
        $mergeData = [];

        if ($this->file('photo')) {
            $mergeData['photo_url'] = $converter->toDataUri($this->file('photo'));
        }

        if ($this->file('store_banner')) {
            $mergeData['store_banner_url'] = $converter->toDataUri($this->file('store_banner'));
        }

        if (! empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (count($this->all()) === 0) {
                $validator->errors()->add('fields', 'At least one field must be provided.');
            }
        });
    }
}
