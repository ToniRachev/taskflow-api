<?php

namespace App\Http\Requests\V1\Board;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BoardUpdateRequest extends FormRequest
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
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('boards')->where('project_id', $this->board->project_id),
            ],
            'description' => ['sometimes', 'string', 'max:500'],
        ];
    }
}
