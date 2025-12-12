<?php

declare(strict_types=1);

namespace App\Enums\Novita;

enum NovitaTrainingStatus: string
{
    case Unknown = 'UNKNOWN';
    case Queuing = 'QUEUING';
    case Training = 'TRAINING';
    case Success = 'SUCCESS';
    case Canceled = 'CANCELED';
    case Failed = 'FAILED';
}
