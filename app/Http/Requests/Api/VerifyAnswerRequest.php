<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VerifyAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question_id' => ['required', 'integer', 'exists:quiz_question,id'],
            'answer_id' => ['required', 'integer', 'exists:quiz_answers,id'],
            'quiz_category_id' => ['required', 'integer', 'exists:quize_categories,id'],
            'quiz_type_id' => ['required', 'integer', 'exists:quiz_type,id'],
        ];
    }
}
