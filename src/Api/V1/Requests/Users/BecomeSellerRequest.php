<?php

declare(strict_types=1);

namespace Src\Api\V1\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class BecomeSellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:10'],
        ];
    }
}
