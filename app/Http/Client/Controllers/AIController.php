<?php

namespace App\Http\Client\Controllers;

use App\Actions\Users\UserOrGuestAction;
use App\Http\Client\Requests\AITxt2ImgRequest;
use App\Models\AIJob;
use App\Models\User;
use App\Services\Novita\Novita;
use Illuminate\Http\JsonResponse;

final class AIController extends Controller
{
    /**
     * @api {post} /api/ai/txt2img 01. Зображення з тексту
     * @apiVersion 1.0.0
     * @apiName AITxt2Img
     * @apiGroup AI
     *
     * @apiParam {String} socket_id UUID сокету клієнта
     * @apiParam {String{1-255}} model_name Назва моделі для генерації
     * @apiParam {String{1-1024}} prompt Текстовий запит для генерації
     * @apiParam {String{1-1024}} [negative_prompt] Негативний промпт (що потрібно уникати)
     * @apiParam {Object[]} [loras] Масив LoRA моделей (макс. 5)
     * @apiParam {String{1-255}} loras[].model_name Назва LoRA моделі
     * @apiParam {Number{0-1}} loras[].strength Сила впливу LoRA
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

        $aiJob = $user->aijobs()->create([
            'type' => AIJob::TYPE_TXT2IMG,
        ]);

        $webhookUrl = route('webhooks.ai.handle', [
            'novita',
            'aiJobId' => $aiJob->id,
        ]);

        $taskId = $novita->txt2img(
            $request->getData(),
            $webhookUrl
        );

        return response()->json([
            'task_id' => $taskId,
            'ai_job_id' => $aiJob->id,
        ], JsonResponse::HTTP_CREATED);
    }
}
