<?php

namespace App\Domain\Woo\Http\Requests;

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
            'merchant_site_id' => ['required', 'string'],
            'woocommerce_user_id' => ['required_without:customer.woocommerce_user_id'],
            'customer' => ['nullable', 'array'],
            'customer.woocommerce_user_id' => ['required_without:woocommerce_user_id'],
            'status' => ['nullable', 'string'],
            'lock_reason' => ['nullable', 'string'],
            'credit' => ['nullable', 'array'],
            'credit.limit' => ['nullable', 'numeric'],
            'credit.days_max' => ['nullable', 'integer'],
            'credit.days_default' => ['nullable', 'integer'],
        ];
    }
}
