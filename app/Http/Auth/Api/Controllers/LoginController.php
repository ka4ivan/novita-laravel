<?php

namespace App\Http\Auth\Api\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

final class LoginController extends Controller
{
    /**
     * @api {post} /api/logout 02. Вихід
     * @apiVersion 1.0.0
     * @apiName AuthLogout
     * @apiGroup Authorisation
     *
     * @apiDescription Завершення сесії авторизованого користувача.
     * Bearer токен буде відкликано на сервері, тому його також потрібно видалити на клієнтській частині.
     *
     * @apiHeader {String} Authorization Токен доступу у форматі: `Bearer {token}`
     *
     * @apiHeaderExample {json} Header-Example:
     * {
     *   "Authorization": "Bearer 10|JPfB9huAMum5CarMreRUHa9tkA775Vp87M0zVmNna4651c72"
     * }
     *
     * @apiSuccessExample {json} Response-Success: HTTP/1.1 200 OK
     * {
     *   "message": "Успішний вихід",
     * }
     */
    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Успішний вихід',
        ]);
    }
}
