<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'beds' => ['required', 'integer', 'min:0', 'max:20'],
            'baths' => ['required', 'integer', 'min:0', 'max:20'],
            'area' => ['required', 'integer', 'min:15', 'max:65535'],
            'city' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20'],
            'street' => ['required', 'string', 'max:150'],
            'street_nr' => ['required', 'string', 'max:20'],
            'price' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:min_width=320,min_height=240,max_width=6000,max_height=6000',
            ],
        ];
    }
}
