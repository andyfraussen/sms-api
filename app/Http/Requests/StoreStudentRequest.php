<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'first_name'       => ['required','string','max:40'],
            'last_name'        => ['required','string','max:40'],
            'date_of_birth'              => ['required','date','before:today'],
            'school_class_id'  => ['required','exists:school_classes,id'],
            'registration_no'  => ['required','string','max:20','unique:students,registration_no'],
        ];
    }
}
