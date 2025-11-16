<?php

namespace App\Http\Client\Controllers\My;

use App\Actions\Ai\NovitaAIModelRefreshResult;
use App\Http\Client\Controllers\Controller;
use App\Http\Client\Requests\AIModelMyRequest;
use App\Http\Client\Resources\AIModelResource;
use App\Models\AIModel;
use App\Models\User;
use App\Services\Novita\Novita;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class AIModelController extends Controller
{
    /**
     * @api {get} /api/ai/my/models 01. Список власних моделей
     * @apiVersion 1.0.0
     * @apiName AIModelMyModels
     * @apiGroup AIModelMy
     *
     * @apiParam {Number} [page=1] Номер сторінки
     * @apiParam {Number} [per_page=15] Кількість елементів на сторінці
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "data": [
     *          {
     *              "id": "019a79c8-32ba-726e-a8a0-1521e2a3e839",
     *              "name": "Моя модель 2",
     *              "base_name": null,
     *              "data": [
     *                  {
     *                      "id": "019a79c8-32c2-724e-aae7-e6a4ec31900c",
     *                      "images": [
     *                          {
     *                              "id": "019a79c8-32c7-7302-8081-ce3f98627250",
     *                              "name": "laravel-logger-1",
     *                              "url": "http://novita.test/storage/019a79c8-32c7-7302-8081-ce3f98627250/laravel-logger-1.png",
     *                              "conversions": {
     *                                  "thumb": {
     *                                      "url": "http://novita.test/storage/019a79c8-32c7-7302-8081-ce3f98627250/conversions/laravel-logger-1-thumb.webp"
     *                                  }
     *                              },
     *                              "states": {
     *                                  "is_favorite": false
     *                              }
     *                          }
     *                      ]
     *                  }
     *              ]
     *          }
     *      ],
     *      "links": {
     *          "first": "http://novita.test/api/ai/my/models?page=1",
     *          "last": "http://novita.test/api/ai/my/models?page=1",
     *          "prev": null,
     *          "next": null
     *      },
     *      "meta": {
     *          "current_page": 1,
     *          "from": 1,
     *          "last_page": 1,
     *          "links": [
     *              {
     *                  "url": null,
     *                  "label": "&laquo; Previous",
     *                  "page": null,
     *                  "active": false
     *              },
     *              {
     *                  "url": "http://novita.test/api/ai/my/models?page=1",
     *                  "label": "1",
     *                  "page": 1,
     *                  "active": true
     *              },
     *              {
     *                  "url": null,
     *                  "label": "Next &raquo;",
     *                  "page": null,
     *                  "active": false
     *              }
     *          ],
     *          "path": "http://novita.test/api/ai/my/models",
     *          "per_page": 15,
     *          "to": 2,
     *          "total": 2
     *      }
     *  }
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $aiModels = $user->aimodels()
            ->latest()
            ->with(['data.media'])
            ->paginate();

        return AIModelResource::collection($aiModels);
    }

    /**
     * @api {get} /api/ai/my/models 02. Створити власну модель
     * @apiVersion 1.0.0
     * @apiName AIModelMyModelsStore
     * @apiGroup AIModelMy
     *
     * @apiParam {String} name Назва моделі
     * @apiParam {String=stable-diffusion-xl-base-1.0,dreamshaperXL09Alpha_alpha2Xl10_91562,protovisionXLHighFidelity3D_release0630Bakedvae_154359,v1-5-pruned-emaonly,epicrealism_naturalSin_121250,chilloutmix_NiPrunedFp32Fix,abyssorangemix3AOM3_aom3a3_10864,dreamshaper_8_93211,WFChild_v1.0,majichenmixrealistic_v10,realisticVisionV51_v51VAE_94301,sdxlUnstableDiffusers_v11_216694,realisticVisionV40_v40VAE_81510,epicrealismXL_v10_247189,somboy_v10_172675,yesmixXL_v10_283329,animagineXLV31_v31_325600} base_model Тип базової моделі
     * @apiParam {File[]} files Масив файлів, які необхідно завантажити
     * @apiParam {String[]} caption Масив підписів для файлів
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "data": {
     *          "message": "Модель успішно створено!"
     *      }
     *  }
     */
    public function store(AIModelMyRequest $request, Novita $novita)
    {
        $user = $request->user();

        /** @var AIModel $aiModel */
        $aiModel = $user->aimodels()->create($request->getData());

        $this->trainingData($request, $aiModel, $novita);

        $aiModel->refresh();

        $AIModelRequest = [
            'name' => $request->input('name'),
            'base_model' => $request->input('base_model'),
            'width' => 1024,
            'height' => 1024,
            'components' => [
                [
                    "name" => "face_crop_region",
                    "args" => [
                        [
                            "name" => "ratio",
                            "value" => "1.4"
                        ]
                    ]
                ],
                [
                    "name" => "resize",
                    "args" => [
                        [
                            "name" => "width",
                            "value" => "512"
                        ],
                        [
                            "name" => "height",
                            "value" => "512"
                        ]
                    ]
                ],
                [
                    "name" => "face_restore",
                    "args" => [
                        [
                            "name" => "method",
                            "value" => "gfpgan_1.4"
                        ],
                        [
                            "name" => "upscale",
                            "value" => "1.0"
                        ]
                    ]
                ]
            ],
            'image_dataset_items' => $aiModel->data->map->only([
                'assets_id',
                'caption',
            ])->toArray()
        ];

//        $aiModel->setAttribute('task_id', $novita->trainingSubject($AIModelRequest));
//        $aiModel->saveOrFail();

//        NovitaAIModelRefreshResult::dispatch($aiModel)
//            ->delay(NovitaAIModelRefreshResult::INTERVAL);

        return response()->json([
            'message' => trans('client.Модель успішно створено!'),
        ]);
    }

    protected function trainingData($request, AIModel $aiModel, Novita $novita): void
    {
        /** @var User $user */
        $user = $request->user();

        $files = Arr::first($request->files);

        if (empty($files)) {
            throw new \InvalidArgumentException('No files provided for training.');
        }

        $i = 0;
        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $extension = strtolower($file->getClientOriginalExtension() ?? '');

            $extension = $extension === 'jpg' ? 'jpeg' : $extension;

            $aiData = $aiModel->data()->create([
                'user_id' => $user->id,
                'extension' => $extension,
                'caption' => $request->input("caption.{$i}"),
            ]);

            $aiData
                ->addMedia($file)
                ->preservingOriginal()
                ->toMediaCollection('image');

            $uploadData = $novita->getTrainingUploadData($extension);

            if (empty($uploadData['assets_id'])) {
                throw new \RuntimeException('Failed to obtain training upload data.');
            }

            $aiData->assets_id = $uploadData['assets_id'];

            if (!$novita->trainingUpload($uploadData, $file)) {
                throw new \RuntimeException('Error uploading training file.');
            }

            $aiData->save();
            $i++;
        }
    }
}
