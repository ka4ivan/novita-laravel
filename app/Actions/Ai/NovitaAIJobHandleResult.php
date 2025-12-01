<?php

namespace App\Actions\Ai;

use App\Events\AITaskSucceed;
use App\Models\AIJob;
use App\Services\Novita\Novita;
use App\Services\Novita\NovitaDownloader;
use App\Support\OperationResult;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\Concerns\AsJob;

class NovitaAIJobHandleResult
{
    use AsAction, AsJob, InteractsWithQueue, SerializesModels;

    public function handle(array $data, AIJob $AIJob): OperationResult
    {
        $status = $data['task']['status'] ?? '';

        if ($status === 'TASK_STATUS_PROCESSING') {
            $AIJob->update([
                'status' => AIJob::STATUS_WAITING,
            ]);

            return OperationResult::warning('Novita: task processing', $data);
        }

        if ($status === 'TASK_STATUS_SUCCEED') {
            $AIJob->update([
                'status' => AIJob::STATUS_DONE,
            ]);

            foreach ($data['images'] as $image) {
                app()->bind(NovitaDownloader::class, function () {
                    return new NovitaDownloader();
                });

                config()->set('media-library.media_downloader', NovitaDownloader::class);

                $AIJob->addMediaFromUrl($image['image_url'])->toMediaCollection('images');
            }

            event(new AITaskSucceed($AIJob->id, $AIJob->task_id));

            return OperationResult::success('Novita: task succeed', $data);
        }


        $AIJob->update([
            'status' => AIJob::STATUS_FAILED,
        ]);

        return OperationResult::error('Novita: unknown task response', $data);
    }
}
