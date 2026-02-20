<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\CampaignApplication\ApplyToCampaignDTO;
use Illuminate\Foundation\Http\FormRequest;

class ApplyToCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string'],
        ];
    }

    public function toDto(): ApplyToCampaignDTO
    {
        return new ApplyToCampaignDTO(
            message: $this->input('message')
        );
    }
}