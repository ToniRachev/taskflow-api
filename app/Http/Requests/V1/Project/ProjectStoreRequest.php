<?php

namespace App\Http\Requests\V1\Project;

use App\Enums\V1\ProjectVisibilityEnum;
use App\Http\Requests\V1\BaseFormRequest;
use Illuminate\Validation\Rule;

class ProjectStoreRequest extends BaseFormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'key' => [
                'sometimes',
                'string',
                'max:10',
                'alpha_num',
                Rule::unique('projects', 'key')
                    ->where('organization_id', $this->route('organization')->id)
            ],
            'description' => ['nullable', 'string'],
            'visibility' => ['sometimes', Rule::enum(ProjectVisibilityEnum::class)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date']
        ];
    }

    public function messages(): array
    {
        return [
            'key.unique' => 'Project key already exists'
        ];
    }
}
