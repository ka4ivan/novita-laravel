<?php

namespace App\Http\Auth\Api\Controllers;

use App\Http\Client\Resources\MediaShowResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function userResource(User $user): array
    {
        $res = $user->only('id', 'name', 'middlename', 'email', 'phone', 'type', 'status');
        $res['avatar'] = null;

        if ($avatar = $user->getMainMedia('avatar')) {
            $res['avatar'] = MediaShowResource::make($avatar);
        }

        return $res;
    }

    protected function authResource(User $user): array
    {
        return [
            'token' => $user->createToken($user->getKey())->plainTextToken,
            'token_type'   => 'bearer',
            'token_expires_in' => null,
            'user' => $this->userResource($user),
        ];
    }
}
