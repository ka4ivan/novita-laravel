<?php

namespace App\Http\Client\Controllers;

use App\Actions\Users\UserOrGuestAction;
use App\Http\Client\Requests\AIImg2ImgRequest;
use App\Http\Client\Requests\AIRemoveBackgroundRequest;
use App\Http\Client\Requests\AIRemoveTextRequest;
use App\Http\Client\Requests\AITxt2ImgRequest;
use App\Http\Client\Requests\AIUpscaleRequest;
use App\Http\Client\Resources\MediaShowResource;
use App\Models\AIJob;
use App\Models\AIModel;
use App\Services\Novita\Novita;
use Illuminate\Http\JsonResponse;

final class AIController extends Controller
{
    /**
     * @api {post} /api/ai/txt2img 01. TXT2IMG
     * @apiVersion 1.0.0
     * @apiName AITxt2Img
     * @apiGroup AI
     *
     * @apiParam {String{1-255}} model_name Назва моделі для генерації
     * @apiParam {String{1-1024}} prompt Текстовий запит для генерації
     * @apiParam {String{1-1024}} [negative_prompt] Негативний промпт (що потрібно уникати)
     * @apiParam {Object[]} [loras] Масив LoRA моделей (макс. 5)
     * @apiParam {String{1-255}} loras.model_name Назва LoRA моделі
     * @apiParam {Number{0-1}} loras.strength Сила впливу LoRA
     * @apiParam {Object} [refiner] Налаштування додаткового рефайнера
     * @apiParam {Number{0-1}} [refiner.switch_at] Поріг перемикання для рефайнера
     * @apiParam {Number{128-2048}} width Ширина зображення
     * @apiParam {Number{128-2048}} height Висота зображення
     * @apiParam {Number{1-8}} image_num Кількість зображень для генерації
     * @apiParam {Number{1-100}} steps Кількість кроків генерації
     * @apiParam {Number=-1+} seed Початкове зерно генерації (-1 — випадкове)
     * @apiParam {Number{1-12}} [clip_skip] Кількість шарів CLIP для пропуску
     * @apiParam {Number{1-30}} guidance_scale Масштаб керування (вплив prompt)
     * @apiParam {String=enum(NovitaSampler)} sampler_name Назва семплера (наприклад: euler, ddim, dpmpp_2m)
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "task_id": "f10333f2-2dd7-4f56-a177-e3c02a774d9a"
     *  }
     */
    public function txt2img(AITxt2ImgRequest $request, Novita $novita)
    {
        $user = UserOrGuestAction::run($request->user(), $request->header('sguest'));

        /** @var AIJob $aiJob */
        $aiJob = $user->aijobs()->create([
            'type' => AIJob::TYPE_TXT2IMG,
        ]);

        // TODO видалити
//        $aiJob->addMediaFromUrl('https://picsum.photos/300')->toMediaCollection('images');
//        $aiJob->addMediaFromUrl('https://picsum.photos/300')->toMediaCollection('images');
//        $aiJob->addMediaFromUrl('https://picsum.photos/300')->toMediaCollection('images');
//        $aiJob->addMediaFromUrl('https://picsum.photos/300')->toMediaCollection('images');

        $webhookUrl = route('webhooks.ai.handle', [
            'novita',
            'aiJobId' => $aiJob->id,
        ]);

        if ($request->input('loras')) {
            $aiModel = AIModel::query()->with('data.media')->where('name', $request->input('loras.0.model_name'))->firstOrFail();
            $aiTrainingData = $aiModel->data->first();
            $media = $aiTrainingData->getFirstMedia('image');

            $taskId = $novita->img2img(
                array_merge($request->getData(), [
                    'image_base64' => $media->toBase64(),
                ]),
                $webhookUrl
            );
        } else {
            $taskId = $novita->txt2img(
                $request->getData(),
                $webhookUrl
            );
        }

        return response()->json([
            'task_id' => $taskId,
            'ai_job_id' => $aiJob->id,
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * @api {post} /api/ai/img2img 02. IMG2IMG
     * @apiVersion 1.0.0
     * @apiName AIImg2Img
     * @apiGroup AI
     *
     * @apiParam {String{1-255}} model_name Назва моделі для генерації
     * @apiParam {String} image_base64 BASE64 зображення
     * @apiParam {String{1-1024}} prompt Текстовий запит для генерації
     * @apiParam {String{1-1024}} [negative_prompt] Негативний промпт (що потрібно уникати)
     * @apiParam {Object} [refiner] Налаштування додаткового рефайнера
     * @apiParam {Number{0-1}} [refiner.switch_at] Поріг перемикання для рефайнера
     * @apiParam {Number{128-2048}} width Ширина зображення
     * @apiParam {Number{128-2048}} height Висота зображення
     * @apiParam {Number{1-8}} image_num Кількість зображень для генерації
     * @apiParam {Number{1-100}} steps Кількість кроків генерації
     * @apiParam {Number=-1+} seed Початкове зерно генерації (-1 — випадкове)
     * @apiParam {Number{1-12}} [clip_skip] Кількість шарів CLIP для пропуску
     * @apiParam {Number{1-30}} guidance_scale Масштаб керування (вплив prompt)
     * @apiParam {String=enum(NovitaSampler)} sampler_name Назва семплера (наприклад: euler, ddim, dpmpp_2m)
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "task_id": "f10333f2-2dd7-4f56-a177-e3c02a774d9a"
     *  }
     */
    public function img2img(AIImg2ImgRequest $request, Novita $novita)
    {
        $user = UserOrGuestAction::run($request->user(), $request->header('sguest'));

        /** @var AIJob $aiJob */
        $aiJob = $user->aijobs()->create([
            'type' => AIJob::TYPE_IMG2IMG,
        ]);

        $webhookUrl = route('webhooks.ai.handle', [
            'novita',
            'aiJobId' => $aiJob->id,
        ]);

        $taskId = $novita->img2img(
            $request->getData(),
            $webhookUrl
        );

        return response()->json([
            'task_id' => $taskId,
            'ai_job_id' => $aiJob->id,
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * @api {post} /api/ai/remove-background 03. Remove Background
     * @apiVersion 1.0.0
     * @apiName AIRemoveBackground
     * @apiGroup AI
     *
     * @apiParam {String} image_file BASE64 зображення
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "data": {
     *          "id": "019a8e84-4be6-7090-8315-6a409b994523",
     *          "name": "media-libraryNrqSty",
     *          "url": "http:\/\/novita.test\/storage\/019a8e84-4be6-7090-8315-6a409b994523\/019a8e84-4bd3-7246-a753-687e7104085b.png",
     *          "conversions": {
     *              "thumb": {
     *                  "url": "http:\/\/novita.test\/storage\/019a8e84-4be6-7090-8315-6a409b994523\/conversions\/019a8e84-4bd3-7246-a753-687e7104085b-thumb.webp"
     *              }
     *          },
     *          "states": {
     *              "is_favorite": false
     *          }
     *      }
     *  }
     */
    public function removeBackground(AIRemoveBackgroundRequest $request, Novita $novita)
    {
        $user = UserOrGuestAction::run($request->user(), $request->header('sguest'));

        $base64 = $novita->removeBackground($request->input('image_file'));

        /** @var AIJob $aiJob */
        $aiJob = $user->aijobs()->create([
            'type' => AIJob::TYPE_REMOVE_BACKGROUND,
        ]);

        $media = $this->mediaFromBase64($aiJob, $base64);

        return MediaShowResource::make($media);
    }

    /**
     * @api {post} /api/ai/remove-text 04. Remove Text
     * @apiVersion 1.0.0
     * @apiName AIRemoveText
     * @apiGroup AI
     *
     * @apiParam {String} image_file BASE64 зображення
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "data": {
     *          "id": "019a8e84-4be6-7090-8315-6a409b994523",
     *          "name": "media-libraryNrqSty",
     *          "url": "http:\/\/novita.test\/storage\/019a8e84-4be6-7090-8315-6a409b994523\/019a8e84-4bd3-7246-a753-687e7104085b.png",
     *          "conversions": {
     *              "thumb": {
     *                  "url": "http:\/\/novita.test\/storage\/019a8e84-4be6-7090-8315-6a409b994523\/conversions\/019a8e84-4bd3-7246-a753-687e7104085b-thumb.webp"
     *              }
     *          },
     *          "states": {
     *              "is_favorite": false
     *          }
     *      }
     *  }
     */
    public function removeText(AIRemoveTextRequest $request, Novita $novita)
    {
        $user = UserOrGuestAction::run($request->user(), $request->header('sguest'));

        $base64 = $novita->removeText($request->input('image_file'));

        /** @var AIJob $aiJob */
        $aiJob = $user->aijobs()->create([
            'type' => AIJob::TYPE_REMOVE_TEXT,
        ]);

        $media = $this->mediaFromBase64($aiJob, $base64);

        return MediaShowResource::make($media);
    }

    /**
     * @api {post} /api/ai/upscale 05. Upscale
     * @apiVersion 1.0.0
     * @apiName AIUpscale
     * @apiGroup AI
     *
     * @apiParam {String=RealESRGAN_x4plus_anime_6B,RealESRNet_x4plus,4x-UltraSharp} model_name Назва моделі
     * @apiParam {String} image_base64 BASE64 зображення
     * @apiParam {Integer=1-4} scale_factor Рівень покращення
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "task_id": "f10333f2-2dd7-4f56-a177-e3c02a774d9a"
     *  }
     */
    public function upscale(AIUpscaleRequest $request, Novita $novita)
    {
        $user = UserOrGuestAction::run($request->user(), $request->header('sguest'));

        /** @var AIJob $aiJob */
        $aiJob = $user->aijobs()->create([
            'type' => AIJob::TYPE_UPSCALE,
        ]);

        $webhookUrl = route('webhooks.ai.handle', [
            'novita',
            'aiJobId' => $aiJob->id,
        ]);

        $taskId = $novita->upscale(
            $request->getData(),
            $webhookUrl
        );

        return response()->json([
            'task_id' => $taskId,
            'ai_job_id' => $aiJob->id,
        ], JsonResponse::HTTP_CREATED);
    }

    private function mediaFromBase64(AIJob $aiJob, string $base64)
    {
        $media = $aiJob
            ->addMediaFromBase64($base64)
            ->usingFileName($aiJob->id . '.png')
            ->toMediaCollection('images');

        $aiJob->setAttribute('status', AIJob::STATUS_DONE);
        $aiJob->save();

        return $media;
    }}
