<?php

namespace App\Domain\Channels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCreditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_id' => ['required', 'string'],
            'platform' => ['nullable', 'string', 'in:woocommerce,shopify,custom,api'],
            'customer' => ['required', 'array'],
            'customer.external_customer_type' => ['nullable', 'string', 'max:50'],
            'customer.external_customer_id' => ['required', 'string', 'max:255'],
            'customer.email' => ['nullable', 'email'],
            'credit' => ['required', 'array'],
            'credit.status' => ['nullable', 'string'],
            'credit.limit_amount' => ['nullable', 'numeric'],
            'credit.days_max' => ['nullable', 'integer'],
            'credit.days_default' => ['nullable', 'integer'],
            'credit.lock_reason' => ['nullable', 'string'],
        ];
    }
}
