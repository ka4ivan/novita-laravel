<?php

namespace App\Http\Client\Controllers;

use App\Http\Client\Requests\AIModelRequest;
use App\Services\Novita\Novita;

final class AIModelController extends Controller
{
    /**
     * @api {post} /api/ai/models 01. Список моделей
     * @apiVersion 1.0.0
     * @apiName AIModelModels
     * @apiGroup AIModel
     *
     * @apiParam {String=checkpoint,lora,vae,controlnet,upscaler,textualinversion} [type] Тип моделі
     * @apiParam {String} [q] Пошуковий запит
     * @apiParam {Boolean=true,false} [sdxl=false] Показати лише SDXL моделі
     * @apiParam {Number{1-500}} [amount=100] Кількість на сторінці
     * @apiParam {String} [cursor] Курсор для пагінації
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "data": [
     *          {
     *              "name": "weight_slider_v2_91681",
     *              "title": "v2.0",
     *              "image": "https://next-app-static.s3.amazonaws.com/images-prod/xG1nkqKTMzGDvpLrqFT7WA/9a60163f-737b-459c-aa6a-db2eb9dc8333/width=450/2190969.jpeg"
     *          },
     *          ...
     *      ],
     *      "next_cursor": "c_100"
     *  }
     */
    public function models(AIModelRequest $request, Novita $novita)
    {
        $query = [
            'filter.types' => $request->input('type'),
            'filter.query' => $request->input('q'),
            'filter.is_sdxl' => $request->input('sdxl'),
            'pagination.limit' => $request->input('amount', 100),
            'pagination.cursor' => $request->input('cursor'),
        ];

        $response = $novita->models($query);

        $models = collect($response['models'] ?? [])
            ->map(fn (array $model) => [
                'name' => $model['sd_name_in_api'] ?? null,
                'title' => $model['name'] ?? null,
                'image' => $model['cover_url'] ?? null,
            ])
            ->values();

        return response()->json([
            'data' => $models,
            'next_cursor' => $response['pagination']['next_cursor'] ?? null,
        ]);
    }
}
