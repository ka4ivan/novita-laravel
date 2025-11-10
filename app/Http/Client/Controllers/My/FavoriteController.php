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
     *   {
     *       "data": [
     *           {
     *               "id": "019a6f55-375d-7204-80fe-c96141b45694",
     *               "name": "300",
     *               "url": "http:\/\/novita.test\/storage\/019a6f55-375d-7204-80fe-c96141b45694\/300.jpeg",
     *               "conversions": {
     *                   "thumb": {
     *                       "url": "http:\/\/novita.test\/storage\/019a6f55-375d-7204-80fe-c96141b45694\/conversions\/300-thumb.webp"
     *                   }
     *               },
     *               "states": {
     *                   "is_favorite": true
     *               }
     *           }
     *       ]
     *   }
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
            ->latest()
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
