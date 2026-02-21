<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\CampaignTier\UpdateCampaignTierDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCampaignTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metric' => ['sometimes', 'in:clicks'],
            'from_value' => ['sometimes', 'integer', 'min:0'],
            'to_value' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'payout_amount' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'nullable', 'string', 'max:10'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (count($this->all()) === 0) {
                $validator->errors()->add('fields', 'At least one field must be provided.');
            }
        });
    }

    public function toDto(): UpdateCampaignTierDTO
    {
        return new UpdateCampaignTierDTO(
            metric: $this->input('metric'),
            fromValue: $this->input('from_value') !== null ? (int) $this->input('from_value') : null,
            toValue: $this->input('to_value') !== null ? (int) $this->input('to_value') : null,
            payoutAmount: $this->input('payout_amount') !== null ? (float) $this->input('payout_amount') : null,
            currency: $this->input('currency')
        );
    }
}