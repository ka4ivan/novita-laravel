<?php

declare(strict_types=1);

namespace App\Enums\Qwen;

enum QwenOutputFormat: string
{
    case OUTPUT_FORMAT_JPEG = 'jpeg';
    case OUTPUT_FORMAT_PNG = 'png';
    case OUTPUT_FORMAT_WEBP = 'webp';
}
