<?php

namespace App\Domain\Channels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerUpsertRequest extends FormRequest
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
            'customer.email' => ['required', 'email'],
            'customer.phone' => ['nullable', 'string'],
            'customer.billing' => ['nullable', 'array'],
            'customer.billing.first_name' => ['nullable', 'string'],
            'customer.billing.last_name' => ['nullable', 'string'],
            'customer.billing.address_1' => ['nullable', 'string'],
            'customer.billing.postcode' => ['nullable', 'string'],
            'customer.billing.country' => ['nullable', 'string'],
        ];
    }
}
