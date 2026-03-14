<?php

namespace App\Http\Requests\V1\Task;

use App\Http\Requests\V1\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskAssigneeRequest extends BaseFormRequest
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
            'assignee_id' => ['nullable', 'exists:users,uuid']
        ];
    }
}
