<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Profile\UpdateBrandProfileDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateBrandProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'brand_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'website_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'industry' => ['sometimes', 'nullable', 'string', 'max:255'],
            'contact_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'contact_phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'description' => ['sometimes', 'nullable', 'string'],
            'logo' => ['sometimes', 'nullable', 'image', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hasAny = $this->hasAny([
                'brand_name','website_url','industry','contact_email','contact_phone','description'
            ]) || $this->hasFile('logo');

            if (! $hasAny) {
                $validator->errors()->add('fields', 'At least one field must be provided.');
            }
        });
    }

    public function toDto(): UpdateBrandProfileDTO
    {
        return new UpdateBrandProfileDTO(
            brandName: $this->input('brand_name'),
            websiteUrl: $this->input('website_url'),
            industry: $this->input('industry'),
            contactEmail: $this->input('contact_email'),
            contactPhone: $this->input('contact_phone'),
            description: $this->input('description'),
            logo: $this->file('logo'),
        );
    }
}