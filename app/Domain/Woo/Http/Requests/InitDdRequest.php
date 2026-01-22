<?php

namespace App\Domain\Woo\Http\Requests;

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
            'merchant_site_id' => ['required', 'string'],
            'order' => ['required', 'array'],
            'order.order_id' => ['required'],
            'order.order_key' => ['required', 'string'],
            'order.amount' => ['required', 'numeric'],
            'order.currency' => ['required', 'string', 'size:3'],
            'customer' => ['required', 'array'],
            'customer.email' => ['required', 'email'],
            'customer.phone' => ['nullable', 'string'],
            'customer.woocommerce_user_id' => ['nullable'],
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
