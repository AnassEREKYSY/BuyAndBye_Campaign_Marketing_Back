<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Src\Infrastructure\Services\Base64ImageConverter;

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

    /**
     * Prepare input data for validation and UseCases.
     * Converts photo and store_banner files to base64 data URIs if present.
     */
    protected function prepareForValidation(): void
    {
        $converter = app(Base64ImageConverter::class);
        $photoUrl = null;
        $bannerUrl = null;

        if ($this->file('photo')) {
            $photoUrl = $converter->toDataUri($this->file('photo'));
        }

        if ($this->file('store_banner')) {
            $bannerUrl = $converter->toDataUri($this->file('store_banner'));
        }

        $this->merge([
            'photo_url' => $photoUrl,
            'store_banner_url' => $bannerUrl,
        ]);
    }
}
