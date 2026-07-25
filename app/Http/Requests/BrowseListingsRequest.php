<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrowseListingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'priceFrom' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'priceTo' => [
                'nullable',
                'integer',
                'min:0',
                'max:4294967295',
                Rule::when($this->filled('priceFrom'), ['gte:priceFrom']),
            ],
            'beds' => ['nullable', 'integer', 'min:0', 'max:20'],
            'baths' => ['nullable', 'integer', 'min:0', 'max:20'],
            'areaFrom' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'areaTo' => [
                'nullable',
                'integer',
                'min:0',
                'max:65535',
                Rule::when($this->filled('areaFrom'), ['gte:areaFrom']),
            ],
        ];
    }
}
