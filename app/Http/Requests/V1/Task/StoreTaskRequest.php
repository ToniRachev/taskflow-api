<?php

namespace App\Http\Requests\V1\Task;

use App\Enums\V1\TaskPriorityEnum;
use App\Enums\V1\TaskStatusEnum;
use App\Enums\V1\TaskTypeEnum;
use App\Http\Requests\V1\BaseFormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTaskRequest extends BaseFormRequest
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
            'assignee_id' => ['nullable', 'exists:users,uuid'],
            'parent_id' => ['nullable', 'exists:tasks,uuid'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', new Enum(TaskTypeEnum::class)],
            'status' => ['nullable', new Enum(TaskStatusEnum::class)],
            'priority' => ['nullable', new Enum(TaskPriorityEnum::class)],
            'story_points' => ['nullable', 'integer', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
