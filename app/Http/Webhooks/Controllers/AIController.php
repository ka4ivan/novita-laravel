<?php

namespace App\Http\Webhooks\Controllers;

use App\Actions\Ai\NovitaHandleWebhook;
use App\Http\Client\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ka4ivan\LaravelLogger\Facades\Llog;

final class AIController extends Controller
{
    public function handle(Request $request, string $ai)
    {
        Llog::info($ai, $request->all());

        $aiJobId = $request->get('aiJobId');

        if ($ai === 'novita') {
            $this->handleNovita($request, $aiJobId);
        }

        return 'ok';
    }

    private function handleNovita(Request $request, string $aiJobId)
    {
        NovitaHandleWebhook::dispatch($request->all(), $aiJobId);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
