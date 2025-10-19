<?php

namespace App\Http\Auth\Api\Controllers;

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
        return $user->only('id', 'name', 'middlename', 'email', 'phone', 'type', 'status');
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
