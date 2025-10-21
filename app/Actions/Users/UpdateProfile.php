<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Arr;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpdateProfile
{
    use AsAction;

    public function handle(User $user, array $data)
    {
        if ($password = Arr::get($data, 'password')) {
            $data['password'] = $password;
        } else {
            unset($data['password']);
        }

        $user->update(Arr::only($data, [
            'name', 'lastname', 'email', 'status', 'password', 'extra'
        ]));

        return $user;
    }
}
