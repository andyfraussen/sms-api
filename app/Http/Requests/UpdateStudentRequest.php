<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
            'first_name' => ['sometimes', 'string', 'max:40'],
            'last_name' => ['sometimes', 'string', 'max:40'],
            'date_of_birth' => ['sometimes', 'date', 'before:today'],
            'school_class_id' => ['sometimes', 'exists:school_classes,id'],
        ];
    }
}
