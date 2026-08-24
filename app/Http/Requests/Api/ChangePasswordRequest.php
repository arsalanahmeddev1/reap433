<?php

namespace App\Http\Requests\Api;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
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
            'old_password' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! Hash::check((string) $value, (string) $this->user()->password)) {
                        $fail('The old password does not match.');
                    }
                },
            ],
            'new_password' => ['required', 'string', 'different:old_password', Password::defaults()],
            'confirm_password' => ['required', 'string', 'same:new_password'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'new_password.different' => 'The new password must be different from the old password.',
            'confirm_password.same' => 'The confirm password does not match the new password.',
        ];
    }
}
