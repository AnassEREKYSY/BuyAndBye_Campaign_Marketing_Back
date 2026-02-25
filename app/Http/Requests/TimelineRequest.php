<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class TimelineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'group' => ['nullable', 'in:day'],
        ];
    }

    public function from(): string
    {
        return Carbon::parse((string) $this->query('from'))->startOfDay()->toDateTimeString();
    }

    public function to(): string
    {
        return Carbon::parse((string) $this->query('to'))->endOfDay()->toDateTimeString();
    }

    public function group(): string
    {
        return (string) ($this->query('group') ?? 'day');
    }
}