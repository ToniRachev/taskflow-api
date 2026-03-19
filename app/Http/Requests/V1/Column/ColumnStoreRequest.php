<?php

namespace App\Http\Requests\V1\Column;

use App\Http\Requests\V1\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class ColumnStoreRequest extends BaseFormRequest
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
            'color' => ['nullable', 'string', 'max:7'],
            'wip_limit' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
