<?php

namespace App\Actions\Ai;

use App\Events\AITaskFailed;
use App\Events\AITaskSucceed;
use App\Models\AIJob;
use App\Services\Novita\NovitaDownloader;
use App\Support\OperationResult;
use Lorisleiva\Actions\Concerns\AsAction;

class NovitaHandleWebhook
{
    use AsAction;

    public function handle(array $data, string $aiJobId): OperationResult
    {
        $aiJob = AIJob::find($aiJobId);
        $eventType = $data['event_type'] ?? null;

        if ($aiJob->status === AIJob::STATUS_DONE) {
            return OperationResult::info('Novita: Task already done');
        }

        if ($eventType !== 'ASYNC_TASK_RESULT') {
            $aiJob->update([
                'status' => AIJob::STATUS_FAILED,
            ]);

            return OperationResult::warning('Novita: unknown event type', [$eventType]);
        }

        $payload = $data['payload'] ?? null;

        $task = $payload['task'];

        if (!in_array($task['task_type'], ['TXT_TO_IMG', 'IMG_TO_IMG', 'UPSCALE'])) {
            $aiJob->update([
                'status' => AIJob::STATUS_FAILED,
            ]);

            return OperationResult::warning('Novita: unknown task type', [$task]);
        }

        if ($task['status'] !== 'TASK_STATUS_SUCCEED') {
            event(new AITaskFailed($aiJobId, $task['task_id']));

            $aiJob->update([
                'status' => AIJob::STATUS_FAILED,
            ]);

            return OperationResult::warning('Novita: task not succeeded', [$task]);
        }

        foreach ($payload['images'] as $image) {
            app()->bind(NovitaDownloader::class, function () {
                return new NovitaDownloader();
            });

            config()->set('media-library.media_downloader', NovitaDownloader::class);

            $aiJob->addMediaFromUrl($image['image_url'])->toMediaCollection('images');
        }

        event(new AITaskSucceed($aiJobId, $task['task_id']));

        $aiJob->update([
            'status' => AIJob::STATUS_DONE,
        ]);

        return OperationResult::info('Novita: Task successfully done', [$aiJobId]);
    }
}
