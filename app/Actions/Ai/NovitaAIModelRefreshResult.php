<?php

namespace App\Actions\Ai;

use App\Enums\Novita\NovitaTrainingModelStatus;
use App\Enums\Novita\NovitaTrainingStatus;
use App\Events\AIModelUpdated;
use App\Mail\AIModelCreated;
use App\Models\AIModel;
use App\Services\Novita\Novita;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ka4ivan\LaravelLogger\Facades\Llog;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\Concerns\AsJob;

class NovitaAIModelRefreshResult
{
    use AsAction, AsJob, InteractsWithQueue, SerializesModels;

    public const INTERVAL = 10;
    private const FINAL_STATUSES = [
        AIModel::STATUS_SUCCESS,
        AIModel::STATUS_CANCELED,
        AIModel::STATUS_FAILED,
    ];

    public int $tries = 90;

    public function __construct(public AIModel $AIModel) {}

    public function handle(Novita $novita): void
    {
        $result = $novita->trainingSubjectResult($this->AIModel->task_id);

        $taskStatus = NovitaTrainingStatus::from($result['task_status']);
        $modelStatus = null;

        if (!empty($result['models'])) {
            $model = $result['models'][0];
            $modelStatus = NovitaTrainingModelStatus::from($model['model_status']);
            $this->AIModel->model_name = $model['model_name'];
        }

        $this->AIModel->extra = $result['extra'] ?? [];

        $this->AIModel->status = match ($taskStatus) {
            NovitaTrainingStatus::Unknown  => AIModel::STATUS_CREATED,
            NovitaTrainingStatus::Queuing  => AIModel::STATUS_QUEUING,
            NovitaTrainingStatus::Training => AIModel::STATUS_TRAINING,
            NovitaTrainingStatus::Canceled => AIModel::STATUS_CANCELED,
            NovitaTrainingStatus::Failed   => AIModel::STATUS_FAILED,
            NovitaTrainingStatus::Success  => match ($modelStatus) {
                NovitaTrainingModelStatus::Deploying => AIModel::STATUS_DEPLOYING,
                NovitaTrainingModelStatus::Serving   => AIModel::STATUS_SUCCESS,
            },
        };

        if ($this->AIModel->isDirty()) {
            $this->AIModel->saveOrFail();
            broadcast(new AIModelUpdated($this->AIModel));
        }

        if (!in_array($this->AIModel->status, self::FINAL_STATUSES, true)) {
            $this->release(self::INTERVAL);
            return;
        }

        $user = $this->AIModel->user;

        Llog::info(__METHOD__, $user->toArray());

        \Mail::to($user->email)->send(new AIModelCreated(
            $user->name ?? $user->lastname ?? '',
            $user->email,
            trans('We are excited to inform you that your AI model has successfully completed its training and is now ready to use! 🎉'),
            $user,
            $this->AIModel
        ));
    }

    public function failed(?\Throwable $exception): void
    {
        $this->AIModel->status = AIModel::STATUS_FAILED;
        $this->AIModel->saveOrFail();
    }
}
