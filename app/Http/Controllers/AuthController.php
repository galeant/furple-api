<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\UploadFileFolder;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\GeneralResource;
use App\Http\Resources\UserResource;
use App\Mail\ForgotPasswordMail;
use App\Mail\RegisterMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Services\AuthService;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    public function login(LoginRequest $request, AuthService $authService)
    {
        $role = match (Route::current()->getPrefix()) {
            'admin' => Role::Admin,
            'user' => Role::User
        };
        $user = $authService->login($request, $role);
        $token = $user->createToken('login-user');

        return (new GeneralResource([
            'user' => new UserResource($user),
            'token' => $token->plainTextToken,
        ]))->additional([
            'message' => __('messages.register')
        ]);
    }

    public function register(
        RegisterRequest $request,
        AuthService $service,
    ) {
        DB::beginTransaction();
        try {

            $user = $service->register($request, Role::User->value);
            $params = [
                'user_id' => $user->id,
                'exp' => time() + (int)config('app.exp_verification_token')
            ];
            Mail::to($request->email)->queue(new RegisterMail(
                token: Crypt::encryptString(serialize($params))
            ));

            DB::commit();

            return (new GeneralResource)->additional([
                'message' => __('messages.register')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function verification($token, AuthService $service)
    {
        $decrypt = Crypt::decryptString($token);
        $decrypt = unserialize($decrypt);

        if ($decrypt['exp'] < time()) {
            throw new \Exception('URL has expired');
        }
        if (Cache::has($token)) {
            abort(404);
        }
        try {

            $service->verification($decrypt);
            Cache::put(
                $token,
                $token,
                $seconds = config('app.exp_verification_token')
            );
            // $url = config('app.frontend_url');
            // $url = $url . '?verification=success';

            // return redirect()->away($url);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception(__('message.user_not_found'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function profile()
    {
        $user = Auth::user();
        return (new UserResource(Auth::user()))->additional([
            'message' => 'Get profile success',
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request, AuthService $service)
    {
        $user = $service->updateProfile(
            user: Auth::user(),
            request: $request,
        );

        return (new UserResource($user))->additional([
            'message' => 'Update profile success',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::user()->tokens()->delete();

        return (new GeneralResource)->additional([
            'message' => 'Logout success',
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request, AuthService $authService)
    {
        DB::beginTransaction();
        try {
            $newPassword = $authService->forgotPassword($request->email);
            Mail::to($request->email)->send(new ForgotPasswordMail(
                name: $newPassword['name'],
                newPassword: $newPassword['password']
            ));

            DB::commit();
            return (new GeneralResource)->additional([
                'message' => 'Forgot password success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function socialLogin($provider)
    {
        $socialite = Socialite::driver($provider);
        if ($provider == 'facebook') {
            $socialite = $socialite->scopes(['email', 'public_profile']);
        }

        return $socialite->stateless()->redirect();
    }

    public function socialLoginCallback($provider, AuthService $service)
    {
        $socialAuth = Socialite::driver($provider)->stateless()->user();
        $socialAuth = Socialite::driver($provider)->userFromToken($socialAuth->token);

        if (!isset($socialAuth->email)) {
            throw new \Exception('Account not found');
        }
        $socialLogin = $service->socialLogin(
            socialAuth: $socialAuth,
            provider: $provider,
            role: Role::User
        );

        $token = $socialLogin->createToken('login-customer');

        return redirect()->away(config('app.frontend_url') . '/social-login?token=' . $token->plainTextToken);
    }
}
