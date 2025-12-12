<?php

declare(strict_types=1);

namespace App\Enums\Novita;

enum NovitaModelUpscale: string
{
    case REALESRGAN_X4PLUS_ANIME_6B = 'RealESRGAN_x4plus_anime_6B';
    case REALESRNET_X4PLUS = 'RealESRNet_x4plus';
    case _4X_ULTRASHARP = '4x-UltraSharp';
}
