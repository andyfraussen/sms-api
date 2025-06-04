<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
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
            'name' => ['required','string','max:40'],
            'type' => ['required','string','max:40'],
            'score' => ['required', 'numeric'],
            'date' => ['required','date'],
            'subject_id' => ['required','exists:subjects,id'],
            'student_id' => ['required','exists:students,id'],
            'graded_by' => ['required','exists:users,id'],
            'comments' => ['required','string'],
        ];
    }
}
