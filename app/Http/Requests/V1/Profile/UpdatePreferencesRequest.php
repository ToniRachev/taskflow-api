<?php

namespace App\Http\Requests\V1\Profile;

use App\Enums\V1\ThemePreferenceEnum;
use App\Helpers\ArrayHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge(ArrayHelper::toSnakeKeys($this->all()));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'theme' => ['sometimes', new Enum(ThemePreferenceEnum::class)],
            'notifications.email' => ['sometimes', 'boolean'],
            'notifications.in_app' => ['sometimes', 'boolean'],
            'notifications.task_assigned' => ['sometimes', 'boolean'],
            'notifications.mentioned' => ['sometimes', 'boolean'],
            'notifications.due_soon' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        $validThemes = implode(', ', array_column(ThemePreferenceEnum::cases(), 'value'));
        return [
            'theme.enum' => "The theme must be one of: $validThemes",
            'notifications.email.boolean' => 'The email notification must be a boolean value.',
            'notifications.in_app.boolean' => 'The in-app notification must be a boolean value.',
            'notifications.task_assigned.boolean' => 'The task assigned notification must be a boolean value.',
            'notifications.mentioned.boolean' => 'The mentioned notification must be a boolean value.',
            'notifications.due_soon.boolean' => 'The due soon notification must be a boolean value.',
        ];
    }
}
