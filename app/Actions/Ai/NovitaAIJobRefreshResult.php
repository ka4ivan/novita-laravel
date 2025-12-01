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

    public const INTERVAL = 10;
    public int $tries = 90;

    public function handle(AIJob $AIJob): void
    {
        $taskResult = (new Novita(config('services.novita.key')))->taskResult($AIJob->task_id);

        /** @var OperationResult $res */
        $res = NovitaAIJobHandleResult::run($taskResult, $AIJob);

        if (!$res->isSuccess()) {
            $this->release(self::INTERVAL);
            return;
        }
    }
}
