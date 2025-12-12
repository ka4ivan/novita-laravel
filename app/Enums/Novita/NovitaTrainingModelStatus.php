<?php

declare(strict_types=1);

namespace App\Enums\Novita;

enum NovitaTrainingModelStatus: string
{
    case Deploying = 'DEPLOYING';
    case Serving = 'SERVING';
}
