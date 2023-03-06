<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Helpers\General;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function login($request, $role)
    {
        $user = User::where('role', $role->value)
            ->where(function ($q) use ($request) {
                $q->where('email', $request->email)
                    ->orWhere('phone', General::parsePhoneNumber($request->phone));
            })->firstOrFail();

        if (Hash::check($request->password, $user->password)) {
            return $user;
        }
        throw new \Exception('Wrong password');
    }

    public function register($request, $role)
    {
        return User::updateOrCreate([
            'email' => $request->email,
            'phone' => General::parsePhoneNumber($request->phone),
        ], [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'role' => $role,
            'password' => Hash::make($request->password),
            'status' => UserStatus::NotVerified,
        ]);
    }

    public function verification($payload)
    {
        $user = User::where([
            'id' => $payload['user_id'],
        ])->firstOrfail();
        $user->update([
            'status' => UserStatus::Active,
        ]);

        return $user->refresh();
    }

    public function updateProfile($user, $request)
    {
        $fill = [
            'email' => $request->email,
            'phone' => General::parsePhoneNumber($request->phone),
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
        ];
        if ($request->filled('password')) {
            $fill['password'] = Hash::make($request->password);
        }
        $user->update($fill);

        return $user->refresh();
    }

    public function forgotPassword($email)
    {
        $user = User::where('email', $email)->firstOrFail();
        $password = Str::random(12);
        $user->update([
            'password' => Hash::make($password),
        ]);

        return [
            'name' => $user->name,
            'password' => $password,
        ];
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
