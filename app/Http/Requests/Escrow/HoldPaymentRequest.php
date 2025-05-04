<?php

namespace App\Http\Requests\Escrow;

use Illuminate\Foundation\Http\FormRequest;

class HoldPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_id' => ['required', 'exists:jobs,id'],
            'amount' => ['required', 'numeric', 'min:1']
        ];
    }
}