<?php

namespace App\Actions\Users;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

// В майбутньому зробити підтримку неавторизованим робити генерацію
class UserOrGuestAction
{
    use AsAction;

    public function handle(?User $user, ?string $sguest): ?User
    {
        if (isset($user)) {
            return $user;
        }

        if (isset($sguest)) {
            return User::find($sguest);
        }

        return null;
    }
}
