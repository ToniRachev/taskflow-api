<?php

namespace App\Http\Requests\V1\Project;

use App\Enums\V1\ProjectVisibilityEnum;
use App\Http\Requests\V1\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends BaseFormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'visibility' => ['sometimes', Rule::enum(ProjectVisibilityEnum::class)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date']
        ];
    }
}
