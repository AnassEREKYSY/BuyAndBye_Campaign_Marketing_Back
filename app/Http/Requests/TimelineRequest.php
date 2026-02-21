<?php

declare(strict_types=1);

namespace App\Http\Requests;

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
        return (string) $this->query('from') . ' 00:00:00';
    }

    public function to(): string
    {
        return (string) $this->query('to') . ' 23:59:59';
    }

    public function group(): string
    {
        return (string) ($this->query('group') ?? 'day');
    }
}