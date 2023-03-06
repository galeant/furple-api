<?php

namespace App\Rules;

use App\Helpers\General;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneValidationRule implements ValidationRule
{
    public function __construct(public $email, public $userId = null)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $existingData = User::where('email', $this->email)
            ->orWhere('phone', General::parsePhoneNumber($value))
            ->first();

        if ($existingData?->phone == $value) {
            $fail('Phone number is already used');
        } else if ($existingData?->email == $this->email) {
            $fail('Email is used by other user');
        }
    }
}
