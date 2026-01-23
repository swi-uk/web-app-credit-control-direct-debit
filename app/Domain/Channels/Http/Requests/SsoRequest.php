<?php

namespace App\Domain\Channels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SsoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_id' => ['required', 'string'],
            'external_customer_id' => ['required', 'string', 'max:255'],
            'external_customer_type' => ['nullable', 'string', 'max:50'],
            'redirect_url' => ['nullable', 'url'],
        ];
    }
}
