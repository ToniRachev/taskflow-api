<?php

namespace App\Http\Requests\V1\Task;

use App\Enums\V1\TaskStatusEnum;
use App\Http\Requests\V1\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskBulkStatusRequest extends BaseFormRequest
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
            'task_ids' => ['required','array', 'min:1'],
            'task_ids.*' => ['required', 'exists:tasks,uuid'],
            'status' => ['required', new Enum(TaskStatusEnum::class)],
        ];
    }
}
