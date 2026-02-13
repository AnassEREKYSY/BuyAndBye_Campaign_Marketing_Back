<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Profile\UpdateUserProfileDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'display_name' => ['sometimes', 'string', 'max:255'],
            'photo' => ['sometimes', 'nullable', 'image', 'max:5120'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'birth_date' => ['sometimes', 'nullable', 'date'],
            'gender' => ['sometimes', 'nullable', 'string', 'max:50'],
            'country_code' => ['sometimes', 'nullable', 'string', 'max:10'],
            'locale' => ['sometimes', 'nullable', 'string', 'max:10'],
            'buyer_categories' => ['sometimes', 'nullable', 'array'],
            'buyer_interests' => ['sometimes', 'nullable', 'array'],
            'payment_methods' => ['sometimes', 'nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hasAnyField =
                $this->hasAny([
                    'display_name',
                    'phone_number',
                    'birth_date',
                    'gender',
                    'country_code',
                    'locale',
                    'buyer_categories',
                    'buyer_interests',
                    'payment_methods',
                ]) || $this->hasFile('photo');

            if (! $hasAnyField) {
                $validator->errors()->add('fields', 'At least one field must be provided.');
            }
        });
    }

    public function toDto(): UpdateUserProfileDTO
    {
        return new UpdateUserProfileDTO(
            displayName: $this->display_name,
            photo: $this->file('photo'),
            phoneNumber: $this->phone_number,
            birthDate: $this->birth_date,
            gender: $this->gender,
            countryCode: $this->country_code,
            locale: $this->locale,
            buyerCategories: $this->buyer_categories,
            buyerInterests: $this->buyer_interests,
            paymentMethods: $this->payment_methods,
        );
    }
}
