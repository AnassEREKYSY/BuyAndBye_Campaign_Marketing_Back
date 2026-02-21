<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\CampaignTier\CreateCampaignTierDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateCampaignTierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'metric' => ['required', 'in:clicks'],
            'from_value' => ['required', 'integer', 'min:0'],
            'to_value' => ['nullable', 'integer', 'min:0'],
            'payout_amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
        ];
    }

    public function toDto(): CreateCampaignTierDTO
    {
        return new CreateCampaignTierDTO(
            metric: $this->metric,
            fromValue: (int) $this->from_value,
            toValue: $this->to_value !== null ? (int) $this->to_value : null,
            payoutAmount: (float) $this->payout_amount,
            currency: $this->currency
        );
    }
}