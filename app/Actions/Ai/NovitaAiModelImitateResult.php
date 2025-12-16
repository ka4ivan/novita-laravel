<?php

namespace App\Actions\Ai;

use App\Enums\Novita\NovitaTrainingModelStatus;
use App\Mail\AIModelCreated;
use App\Models\AIModel;
use Illuminate\Support\Collection;
use Ka4ivan\LaravelLogger\Facades\Llog;
use Lorisleiva\Actions\Concerns\AsAction;

class NovitaAiModelImitateResult
{
    use AsAction;

    public function handle()
    {
        AIModel::query()
            ->whereIn('status', [AIModel::STATUS_CREATED, AIModel::STATUS_TRAINING])
            ->where('progress', '<', 100)
            ->chunk(10, function (Collection $chunk) {
                /** @var AIModel $aiModel */
                foreach ($chunk as $aiModel) {
                    $aiModel->increment('progress');
                    $aiModel->setAttribute('status', AIModel::STATUS_TRAINING);
                    $aiModel->save();

                    if ($aiModel->progress >= 100) {
                        $aiModel->setAttribute('status', AIModel::STATUS_SUCCESS);
                        $aiModel->save();

                        $user = $aiModel->user;

                        \Mail::to($user->email)->send(new AIModelCreated(
                            $user->name ?? $user->lastname ?? '',
                            $user->email,
                            trans('We are excited to inform you that your AI model has successfully completed its training and is now ready to use! 🎉'),
                            $user,
                            $aiModel
                        ));
                    }
                }
            });
    }
}
