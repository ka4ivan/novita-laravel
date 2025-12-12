<?php

namespace App\Providers;

use App\Models\AIModel;
use App\Services\Novita\Novita;
use App\Support\Favorites\Favorite;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $loader = AliasLoader::getInstance();

        $this->app->bind(Novita::class, static function () {
            return new Novita(config('services.novita.key'));
        });

        $this->app->singleton(Favorite::class, function () {
            return new Favorite();
        });
        $loader->alias('Favorite', \App\Support\Favorites\Facades\Favorite::class);
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

        Route::model('aiModel', AIModel::class);

        $this->setMorphMap();
    }

    protected function setMorphMap(): void
    {
        Relation::morphMap([
            'ai_job' => \App\Models\AIJob::class,
            'ai_model' => \App\Models\AIModel::class,
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
