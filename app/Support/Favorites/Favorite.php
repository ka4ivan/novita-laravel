<?php

namespace App\Support\Favorites;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Favorite
{
    protected array|null $favoriteModelIds = null;

    public function getFavoriteModels(): array
    {
        /** @var User $user */
        if ($user = auth()->user()) {
            if (is_null($this->favoriteModelIds)) {
                $this->favoriteModelIds = $user->favorites->pluck('model_id')->toArray();
            }

            return $this->favoriteModelIds;
        }

        return [];
    }

    /**
     * @param $model
     * @return bool
     */
    public function isFavorite($model): bool
    {
        $id = $model instanceof Model ? $model->id : $model;

        return in_array($id, $this->getFavoriteModels());
    }

    /**
     * Кількість позицій в обраних.
     *
     * @return int
     */
    public function getQty(): int
    {
        return count($this->getFavoriteModels()) ?? 0;
    }
}
