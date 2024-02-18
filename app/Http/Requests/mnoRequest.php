<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class mnoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'initiator_email' => 'required',
            'request_reference' => 'required',
            'client_mobile' => '',
            'amount' => 'required',
            'network' => '',
            'trx_type' => 'required',
            'status' => 'required',
        ];
    }

    
    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'amount.required' => 'Amount is required!',
            'mobilenumber.required' => 'Mobile is required!'
        ];
    }
}
