<?php

namespace App\Actions\Ai;

use App\Events\AITaskFailed;
use App\Events\AITaskSucceed;
use App\Models\AIJob;
use App\Services\Novita\Novita;
use App\Services\Novita\NovitaDownloader;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\Concerns\AsJob;

class NovitaAIGeminiHandleResult
{
    use AsAction, AsJob, InteractsWithQueue, SerializesModels;

    public function __construct(public AIJob $AIJob) {}

    public function handle(array $data): void
    {
        \Llog::info($data);
        $task = (new Novita(config('services.novita.key')))
            ->gemini3ProImageEdit($data);

        foreach ($task['image_urls'] ?? [] as $imageUrl) {
            config()->set('media-library.media_downloader', NovitaDownloader::class);
            $this->AIJob->addMediaFromUrl($imageUrl)->toMediaCollection('images');
        }

        event(new AITaskSucceed($this->AIJob->id, $this->AIJob->task_id));

        $this->AIJob->update([
            'status' => AIJob::STATUS_DONE,
        ]);
    }

    public function failed(?\Throwable $exception): void
    {
        $this->AIJob->update(['status' => AIJob::STATUS_FAILED]);
        event(new AITaskFailed($this->AIJob->id, $this->AIJob->task_id));
    }
}
