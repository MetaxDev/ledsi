<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskStoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        {
            return [
                'title' => ['required', 'string', 'max:255'],
                'status' => ['sometimes', 'integer', Rule::in(TaskStatus::values())],
            ];
        }
    }

    /**
     *  Сообщения
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Тайтл обязателен.',
            'title.string' => 'Тайтл должен быть строкой.',
            'title.max' => 'Тайтл не должен быть больше 255 символов.',
            'status.integer' => 'Статус должен быть цифрой.'
        ];
    }
}
