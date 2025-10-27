<?php

namespace App\Http\Client\Controllers\My;

use App\Actions\Users\UpdateProfileAction;
use App\Http\Client\Controllers\Controller;
use App\Http\Client\Requests\ProfileRequest;
use App\Http\Client\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\Request;

final class ProfileController extends Controller
{
     /**
     *  @api {get} /api/my/profile 01. Форма редагування
     *  @apiVersion 1.0.0
     *  @apiName ProfileEdit
     *  @apiGroup Profile
     *
     *  @apiDescription Отримати поточний профіль користувача разом із доступними способами оплати та доставки.
     *
     *  @apiHeader {String} Authorization Bearer токен доступу: `Bearer {token}`
     *
     *  @apiSuccessExample {json} Response-Success: HTTP/1.1 200 OK
     *  {
     *      "data": {
     *          "id": "2e265cc5-a6b1-4e8f-b3f2-810a9060885c",
     *          "name": "Тарас",
     *          "lastname": "Шевченко",
     *          "email": "test@gmail.com",
     *          "status": "active",
     *          "balance": 10.25,
     *          "created_at": "2025-06-09T12:41:29.000000Z",
     *          "avatar": {
     *              "id": "08575187-c317-4a11-9366-f983fadbb155",
     *              "name": "2025-06-18_16-59",
     *              "url": "http://test.test/storage/08575187-c317-4a11-9366-f983fadbb155/2025-06-18-16-59.jpg"
     *          },
     *      }
     *  }
     */
    public function edit(Request $request)
    {
        /** @var User $user */
        $user = $request->user()->load('media', 'socialites');

        return (ProfileResource::make($user));
    }

    /**
     *  @api {post} /api/my/profile 02. Оновити дані
     *  @apiVersion 1.0.0
     *  @apiName ProfileUpdate
     *  @apiGroup Profile
     *
     *  @apiDescription Оновлення профілю користувача. Поля можна передавати поля довільно при потребі.
     *  Якщо певне поле відсутнє в запиті, воно не буде валідуватися чи змінюватися.
     *
     *  @apiHeader {String} Authorization Bearer токен: `Bearer {token}`
     *
     *  @apiParamExample {json} Request-Body:
     *  {
     *      "name": "Тарас",
     *      "lastname": "Шевченко",
     *      "email": "test@gmail.com",
     *      "avatar": {
     *          "id": "8b8e2e64-d144-45f7-b5a3-dc3dff2bcd7f"
     *      }
     *  }
     *
     *  @apiSuccessExample {json} Response-Success: HTTP/1.1 200 OK
     *  {
     *      "message": "Дані успішно оновлено!"
     *  }
     */
    public function update(ProfileRequest $request)
    {
        /** @var User $user */
        $user = $request->user();

        UpdateProfileAction::run($user, $request->all());

        $user->mediaManageRefresh($request->all());

        return response()
            ->json(['message' => trans('alerts.update.success')]);
    }

    /**
     *  @api {delete} /api/my/profile 03. Видалити профіль
     *  @apiVersion 1.0.0
     *  @apiName ProfileDelete
     *  @apiGroup Profile
     *
     *  @apiDescription Видалення особистого профілю користувача.
     *
     *  @apiHeader {String} Authorization Bearer токен: `Bearer {token}`
     *
     *  @apiSuccessExample {json} Response-Success: HTTP/1.1 200 OK
     *  {
     *      "message": "Дані успішно видалено!"
     *  }
     */
    public function delete(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => trans('alerts.destroy.success')]);
    }
}
