<?php

namespace App\Support\Favorites\Facades;

class Favorite extends \Illuminate\Support\Facades\Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \App\Support\Favorites\Favorite::class;
    }
}
