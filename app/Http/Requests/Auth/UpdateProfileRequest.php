<?php

namespace App\Http\Requests\Auth;

use App\Enums\Gender;
use App\Rules\PhoneValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $user = auth()->user();
        return [
            'firstname' => 'required',
            'lastname' => 'required',
            'birthday' => [
                'required',
                'date',
                'date_format:Y-m-d'
            ],
            'gender' => [
                'required',
                'in:' . implode(',', Gender::getAllValue())
            ],
            'email' => [
                'required',
                'email',
                'unique:users,email,' . $user->id,
            ],
            'phone' => [
                'required',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'unique:users,phone,' . $user->id,
            ],
            'password' => 'required',
            'confirm_password' => [
                'sometimes:password',
                'required_with:password',
                'same:password',
            ],
        ];
    }
}
