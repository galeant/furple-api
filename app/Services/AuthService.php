<?php

namespace App\Service;

use App\Enums\UserCreateFrom;
use App\Enums\UserStatus;
use App\Helpers\General;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;

class AuthService
{
    public function login($request, $role)
    {
        $user = User::where([
            'email' => $request->email,
            'role' => $role,
        ])->first();

        if (!$user) {
            throw new UnauthorizedException();
        }

        if (Hash::check($request->password, $user->password)) {
            return $user;
        }
        throw new \Exception('Wrong password');
    }

    public function registerByWeb($request, $role)
    {
        $referalCode = Str::random(4);
        while (User::where('referal_code', $referalCode)->exists()) {
            $referalCode = Str::random(4);
        }

        $user = User::updateOrCreate([
            'email' => $request->email,
        ], [
            'name' => $request->name,
            'phone' => General::parsePhoneNumber($request->phone),
            'role' => $role,
            'password' => Hash::make($request->password),
            'status' => UserStatus::NotVerified,
            'referal_code' => $referalCode,
        ]);

        return $user;
    }

    public function verification($payload)
    {
        $user = User::where([
            'id' => $payload->id,
        ])->firstOrfail();
        $user->update([
            'status' => UserStatus::Active,
        ]);

        return $user->refresh();
    }

    public function updateProfile($user, $request)
    {
        $fill = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
        ];
        if ($request->filled('password')) {
            $fill['password'] = Hash::make($request->password);
        }
        $user->update($fill);

        return $user->refresh();
    }

    public function forgotPassword($email)
    {
        return User::where('email', $email)->firstOrFail();
    }

    public function socialLogin($socialAuth, $provider, $role)
    {
        $field = match ($provider) {
            'google' => 'google_token',
            'facebook' => 'facebook_token',
        };

        $user = User::where('email', $socialAuth->email)
            ->first();

        $fill = [
            'role' => $role,
            'name' => $socialAuth->name,
            'status' => UserStatus::Active,
            $field => $socialAuth->token,
        ];
        if ($user?->name !== null) {
            unset($fill['name']);
        }

        $user = User::firstOrCreate([
            'email' => $socialAuth->email,
        ], $fill);

        return $user;
    }
}
