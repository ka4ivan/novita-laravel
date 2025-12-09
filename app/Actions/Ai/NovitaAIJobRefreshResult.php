<?php

namespace App\Actions\Ai;

use App\Models\AIJob;
use App\Services\Novita\Novita;
use App\Support\OperationResult;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\Concerns\AsJob;

class NovitaAIJobRefreshResult
{
    use AsAction, AsJob, InteractsWithQueue, SerializesModels;

    public int $tries = 20;
    public int $backoff = 10;

    public function handle(AIJob $AIJob)
    {
        $taskResult = (new Novita(config('services.novita.key')))
            ->taskResult($AIJob->task_id);

        /** @var OperationResult $res */
        $res = NovitaAIJobHandleResult::make()->run($taskResult, $AIJob);

        if (!$res->isSuccess()) {
            self::dispatch($AIJob)->delay(now()->addSeconds($this->backoff));
            return;
        }
    }
}
