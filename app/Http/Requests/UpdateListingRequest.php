<?php

namespace App\Http\Requests;

class UpdateListingRequest extends StoreListingRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['image']);

        return $rules;
    }
}
