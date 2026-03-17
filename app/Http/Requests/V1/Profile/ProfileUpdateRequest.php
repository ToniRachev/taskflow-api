<?php

namespace App\Http\Requests\V1\Profile;

use App\Helpers\ArrayHelper;
use App\Http\Requests\V1\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends BaseFormRequest
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
            'bio' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'regex:/^\+?[1-9]\d{6,14}$/'],
            'github_url' => ['nullable', 'regex:/^https:\/\/(www\.)?github\.com\/[a-zA-Z0-9\-]+\/?$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'The phone number must be a valid phone number.',
            'github_url.regex' => 'The GitHub URL must be a valid URL.',
        ];
    }
}
