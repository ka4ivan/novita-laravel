<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;

class Handler
{
    public function __invoke(Schedule $schedule): void
    {
        $schedule->call(function () {
            \App\Actions\Ai\NovitaAiModelImitateResult::dispatch();
        })->everyFiveMinutes();
    }
}
