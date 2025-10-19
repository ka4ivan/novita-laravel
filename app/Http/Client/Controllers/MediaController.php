<?php

namespace App\Http\Client\Controllers;

use App\Http\Client\Resources\MediaUploadResource;
use App\Http\Client\Requests\MediaUploadRequest;
use Fomvasss\MediaLibraryExtension\Actions\DeleteMediaFile;
use Fomvasss\MediaLibraryExtension\Actions\UploadMediaTemporaryFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class MediaController  extends Controller
{
    /**
     * @api {post} /api/media 01. Завантажити
     * @apiVersion 1.0.0
     * @apiName MediaUpload
     * @apiGroup Media
     *
     * @apiParam {File} file Файл
     * @apiParam {String} url або Url на файл
     * @apiParam {Boolean=true,false} [is_main=false] Зробити головним (для колекції)
     * @apiParam {String=photos,avatar,image,etc...} [collection_name] Назва колекції
     *
     * @apiParamExample {json} Request-Example:
     * {
     *    file: "<BINARY FILE>",
     *    is_main: true,
     *    collection_name: "photos",
     * }
     *
     * @apiParamExample {json} Request-Example:
     * {
     *    url: "http://test.com/image.jpg"
     *    is_main: true,
     *    collection_name: "avatar",
     * }
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *   {
     *       "data": {
     *           "id": 20,
     *           "url": "http://site.test/storage/47/avesome-666.jpg",
     *           "name": "avesome-666",
     *           "mime_type": "image/jpeg",
     *           "extension": "jpg",
     *           "size": 1024
     *       }
     *   }
     *
     */
    public function upload(MediaUploadRequest $request)
    {
        $media = (new UploadMediaTemporaryFile)->handle($request->validated());

        return new MediaUploadResource($media);
    }

    /**
     * @api {delete} /api/media/{id} 02. Видалити
     * @apiVersion 1.0.0
     * @apiName MediaDelete
     * @apiGroup Media
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 202 Accepted
     *    {
     *        "message": "Дані успішно видалено!"
     *    }
     */
    public function delete(Media $media)
    {
        if ((new DeleteMediaFile())->handle($media)) {
            return response()
                ->json(['message' => \trans('alerts.destroy.success')])
                ->setStatusCode(\Illuminate\Http\Response::HTTP_ACCEPTED);
        }

        throw new NotFoundHttpException(\trans('client.destroy.error'));
    }
}
