<?php

declare(strict_types=1);

namespace Src\Api\V1\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Src\Infrastructure\Services\Base64ImageConverter;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'display_name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /**
     * Prepare input data for validation and UseCases.
     * Converts photo file to base64 data URI if present.
     */
    protected function prepareForValidation(): void
    {
        $converter = app(Base64ImageConverter::class);
        $photoUrl = null;

        if ($this->file('photo')) {
            $photoUrl = $converter->toDataUri($this->file('photo'));
        }

        $this->merge([
            'photo_url' => $photoUrl,
        ]);
    }
}
