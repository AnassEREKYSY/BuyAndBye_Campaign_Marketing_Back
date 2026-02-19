<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Campaign\CreateCampaignDTO;
use Illuminate\Foundation\Http\FormRequest;

class CreateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'uuid'],
            'title' => ['required', 'string', 'max:255'],
            'objective' => ['nullable', 'string'],
            'commission_type' => ['required', 'in:percent,fixed'],
            'commission_value' => ['required', 'numeric', 'min:0'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
        ];
    }

    public function toDto(): CreateCampaignDTO
    {
        return new CreateCampaignDTO(
            productId: $this->product_id,
            title: $this->title,
            objective: $this->objective,
            commissionType: $this->commission_type,
            commissionValue: (float) $this->commission_value,
            budget: $this->budget !== null ? (float) $this->budget : null,
            startAt: $this->start_at,
            endAt: $this->end_at,
        );
    }
}