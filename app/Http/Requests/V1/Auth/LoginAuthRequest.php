<?php

namespace App\Http\Requests\V1\Auth;

use App\Http\Requests\V1\BaseFormRequest;

class LoginAuthRequest extends BaseFormRequest
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
            'email' => ['required', 'email:rfc,dns', 'string'],
            'password' => ['required', 'string'],
        ];
    }
}
