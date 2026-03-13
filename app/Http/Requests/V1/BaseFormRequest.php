<?php

namespace App\Http\Requests\V1;

use App\Helpers\ArrayHelper;
use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge(ArrayHelper::toSnakeKeys($this->all()));
    }
}
