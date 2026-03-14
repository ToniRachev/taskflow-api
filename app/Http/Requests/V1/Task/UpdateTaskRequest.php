<?php

namespace App\Http\Requests\V1\Task;

use App\Enums\V1\TaskTypeEnum;
use App\Http\Requests\V1\BaseFormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends BaseFormRequest
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
            'parent_id' => ['nullable', 'exists:tasks,uuid'],
            'title' => ['filled', 'string', 'min:1', 'max:255'],
            'description' => ['filled', 'min:3', 'string'],
            'type' => ['filled', new Enum(TaskTypeEnum::class)],
            'story_points' => ['nullable', 'integer', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
