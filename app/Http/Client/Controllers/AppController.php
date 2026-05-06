<?php

namespace App\Http\Client\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

final class AppController extends Controller
{
    /**
     * @api {post} /api/app/glob 01. Глобальні дані
     * @apiVersion 1.0.0
     * @apiName AppGlob
     * @apiGroup App
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "user": {
     *          "id": "a3e22e6-329e-46ba-948f-670dbea5eb1f",
     *          "email": "bob@app.com",
     *          "fullname": "Bob Null"
     *      }
     *  }
     */
    public function glob(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            // дані авторизованого користувача/гостя
            'user' => $user ? [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'email' => $user->email,
            ] : null,
        ]);
    }
}
