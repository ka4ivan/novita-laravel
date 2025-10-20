<?php

namespace App\Providers;

use App\Services\Novita\Novita;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Novita::class, static function () {
            return new Novita(config('services.novita.key'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($url = env('NGROK_URL')) {
            URL::forceScheme(env('NGROK_SCHEME') ?: 'https');
            URL::forceRootUrl($url);
        }

        $this->setMorphMap();
    }

    protected function setMorphMap(): void
    {
        Relation::morphMap([
            'ai_model' => \App\Models\AiModel::class,
            'ai_training' => \App\Models\AITraining::class,
            'ai_training_data' => \App\Models\AITrainingData::class,
            'favorite' => \App\Models\Favorite::class,
            'media' => \App\Models\Media::class,
            'payment' => \App\Models\Payment::class,
            'socialite' => \App\Models\Socialite::class,
            'user' => \App\Models\User::class,
        ]);
    }
}
