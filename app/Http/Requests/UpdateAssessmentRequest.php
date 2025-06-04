<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssessmentRequest extends FormRequest
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
            'name' => ['sometimes','string','max:40'],
            'type' => ['sometimes','string','max:40'],
            'score' => ['sometimes', 'numeric'],
            'date' => ['sometimes','date'],
            'subject_id' => ['sometimes','exists:subjects,id'],
            'student_id' => ['sometimes','exists:students,id'],
            'graded_by' => ['sometimes','exists:users,id'],
            'comments' => ['sometimes','string'],
        ];
    }
}
