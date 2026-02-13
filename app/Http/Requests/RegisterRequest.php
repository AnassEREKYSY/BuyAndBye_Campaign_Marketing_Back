<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Auth\RegisterUserDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
            'display_name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function toDto(): RegisterUserDTO
    {
        return new RegisterUserDTO(
            email: $this->email,
            password: $this->password,
            displayName: $this->display_name,
            photo: $this->file('photo'),
        );
    }
}
