<?php

namespace App\Http\Requests\Auth;

use App\Helpers\General;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'nullable',
                'exists:users,email'
            ],
            'phone' => [
                'required_without:email',
                function ($attribute, $value, $fail) {
                    $value = General::parsePhoneNumber($value);
                    if (!User::where('phone', $value)->exists()) {
                        $fail('Phone not registered');
                    }
                },
            ],
            'password' => [
                'required'
            ],
        ];
    }
}
