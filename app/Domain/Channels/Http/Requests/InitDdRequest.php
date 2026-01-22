<?php

namespace App\Domain\Channels\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InitDdRequest extends FormRequest
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
            'order' => ['required', 'array'],
            'order.external_order_type' => ['nullable', 'string', 'max:50'],
            'order.external_order_id' => ['required', 'string', 'max:255'],
            'order.external_order_key' => ['nullable', 'string', 'max:255'],
            'order.amount' => ['required', 'numeric'],
            'order.currency' => ['required', 'string', 'size:3'],
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
            'return_urls' => ['required', 'array'],
            'return_urls.success' => ['required', 'url'],
            'return_urls.cancel' => ['required', 'url'],
        ];
    }
}
