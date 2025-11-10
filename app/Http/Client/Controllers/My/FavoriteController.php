<?php

namespace App\Http\Client\Controllers\My;

use App\Http\Client\Controllers\Controller;
use App\Http\Client\Resources\FavoriteResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\Request;

final class FavoriteController extends Controller
{

    /**
     * @api {get} /api/my/favorites/medias 1. Список обраних зображень
     * @apiVersion 1.0.0
     * @apiName FavoritesMedias
     * @apiGroup Favorites
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "data": [
     *          {
     *              "id": "08575187-c317-4a11-9366-f983fadbb155",
     *              "name": "2025-06-18_16-59",
     *              "url": "http://test.test/storage/08575187-c317-4a11-9366-f983fadbb155/2025-06-18-16-59.jpg"
     *          }
     *      ]
     *  }
     *
     * @apiErrorExample {json} Response-Error: HTTP/1.1 401 Unauthorized
     *  {
     *     "message": "Unauthenticated"
     *  }
     */
    public function medias(Request $request)
    {
        $user = $request->user();

        $items = $user->favoriteMedias()
            ->with(['model'])
            ->get();

        return FavoriteResource::collection($items);
    }

    /**
     * @api {post} /api/my/favorites/medias/{media:id} 2. Обрати/Видалити зображення
     * @apiVersion 1.0.0
     * @apiName FavoritesMedia
     * @apiGroup Favorites
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "data": {
     *          "result": "added", // added | deleted
     *          "message": "Успішно додано в обране!"
     *      }
     *  }
     *
     * @apiErrorExample {json} Response-Error: HTTP/1.1 401 Unauthorized
     *  {
     *     "message": "Unauthenticated"
     *  }
     */
    public function media(Request $request, Media $media)
    {
        /** @var User $user */
        $user = $request->user();
        $res = $user->toggleFavorite($media);

        return response()->json([
            'result' => $res,
            'message' => $res === 'added' ? trans('client.favorites.add') : trans('client.favorites.delete'),
        ]);
    }
}
