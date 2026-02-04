<?php

declare(strict_types=1);

namespace Src\Api\V1\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_token' => ['required', 'string'],
        ];
    }

    /**
     * Prepare input data for validation and UseCases.
     * Normalize snake_case from request to camelCase for DTOs.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'idToken' => $this->input('id_token'),
        ]);
    }
}
