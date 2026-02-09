<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\Base64ImageConverter;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'display_name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'photo_url' => ['nullable', 'string'],
        ];
    }

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
