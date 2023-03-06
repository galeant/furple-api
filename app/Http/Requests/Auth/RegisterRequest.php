<?php

namespace App\Http\Requests\Auth;

use App\Enums\Gender;
use App\Models\User;
use App\Rules\PhoneValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
                'unique:users,email'
            ],
            'phone' => [
                'required',
                'unique:users,phone',
                new PhoneValidationRule($this->email)
            ],
            'password' => 'required',
            'confirm_password' => ['required', 'same:password']
        ];
    }
}
