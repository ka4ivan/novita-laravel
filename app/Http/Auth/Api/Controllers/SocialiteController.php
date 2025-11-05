<?php

namespace App\Http\Auth\Api\Controllers;

use App\Events\UserRegistered;
use App\Models\Socialite as SocialiteModel;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

final class SocialiteController extends Controller
{
    /**
     *  @api {post} /api/socialite/{provider} 01. Socialite: Логін
     *  @apiVersion 1.0.0
     *  @apiName AuthSocialite
     *  @apiGroup Authorisation
     *
     *  @apiDescription Авторизація або реєстрація користувача через зовнішні соц. сервіси.<br>
     *  Необхідно передати Access Token, отриманий із SDK обраної соцмережі.<br>
     *  У відповідь клієнт отримає Bearer Token для подальших запитів.
     *
     *  @apiBody {String=google} provider Назва соцмережі (у URI-параметрі)
     *  @apiBody {String} access_token Access Token, отриманий від соц. мережі (SDK)
     *
     *  @apiParamExample {json} Body-Example:
     *  {
     *      "access_token": "ya29.a0AfH6SMB...xyz"
     *  }
     *
     *  @apiSuccessExample {json} Success-Response: HTTP/1.1 200 OK
     *  {
     *      "message": "Успішний вхід",
     *      "state": "active",
     *      "token": "11|sUf12wgUIasRriR2Zc1JAMYzbF5S5CaQG5RJJelP80e4fedf",
     *      "token_type": "bearer",
     *      "token_expires_in": null,
     *      "user": {
     *          "id": "f5ba6b19-7bb3-4915-baa1-beb4bcde063b",
     *          "name": "Тарас",
     *          "lastname": null,
     *          "middlename": "Григорович",
     *          "email": "test@gmail.com",
     *          "phone": null,
     *          "status": "active"
     *      }
     *  }
     */
    public function socialLogin(Request $request, string $provider)
    {
        $data = $request->validate([
            'access_token' => 'required|string',
        ]);

        $providerUser = Socialite::driver($provider)->userFromToken($data['access_token']);

        $socialAccount = SocialiteModel::query()
            ->where('provider', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if ($socialAccount) {
            return $this->respondWithAuth($socialAccount->user);
        }

        $user = User::query()
            ->firstOrCreate([
                'email' => $providerUser->getEmail(),
                ], [
                    'name' => $providerUser->getName(),
                    'registered_at' => now(),
                ]);

        if ($user->wasRecentlyCreated) {
            if ($avatar = $providerUser->getAvatar()) {
                $user->addMediaFromUrl($avatar)->toMediaCollection('avatar');
            }

            event(new UserRegistered($user));
        }

        $user->socialites()->firstOrCreate([
            'provider_id' => $providerUser->getId(),
            'provider' => $provider,
        ], [
            'avatar' => $providerUser->getAvatar(),
        ]);

        return $this->respondWithAuth($user);
    }

    private function respondWithAuth(User $user)
    {
        return response()->json([
                'message' => trans('auth.login'),
                'state' => 'active',
            ] + $this->authResource($user));
    }
}
