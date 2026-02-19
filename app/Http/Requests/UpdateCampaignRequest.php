<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\Dtos\Campaign\UpdateCampaignDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'objective' => ['sometimes', 'nullable', 'string'],
            'commission_type' => ['sometimes', 'in:percent,fixed'],
            'commission_value' => ['sometimes', 'numeric', 'min:0'],
            'budget' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'start_at' => ['sometimes', 'nullable', 'date'],
            'end_at' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', 'nullable', 'in:draft,published,closed'],
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

    public function toDto(): UpdateCampaignDTO
    {
        return new UpdateCampaignDTO(
            title: $this->input('title'),
            objective: $this->input('objective'),
            commissionType: $this->input('commission_type'),
            commissionValue: $this->input('commission_value') !== null ? (float) $this->input('commission_value') : null,
            budget: $this->input('budget') !== null ? (float) $this->input('budget') : null,
            startAt: $this->input('start_at'),
            endAt: $this->input('end_at'),
            status: $this->input('status'),
        );
    }
}