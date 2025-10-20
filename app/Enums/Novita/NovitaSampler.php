<?php

declare(strict_types=1);

namespace App\Enums\Novita;

enum NovitaSampler: string
{
    case EULER_A = 'Euler a';
    case EULER = 'Euler';
    case LMS = 'LMS';
    case HEUN = 'Heun';
    case DPM2 = 'DPM2';
    case DPM2_A = 'DPM2 a';
    case DPMPP_2S_A = 'DPM++ 2S a';
    case DPMPP_2M = 'DPM++ 2M';
    case DPMPP_SDE = 'DPM++ SDE';
    case DPM_FAST = 'DPM fast';
    case DPM_ADAPTIVE = 'DPM adaptive';
    case LMS_KARRAS = 'LMS Karras';
    case DPM2_KARRAS = 'DPM2 Karras';
    case DPM2_A_KARRAS = 'DPM2 a Karras';
    case DPMPP_2S_A_KARRAS = 'DPM++ 2S a Karras';
    case DPMPP_2M_KARRAS = 'DPM++ 2M Karras';
    case DPMPP_SDE_KARRAS = 'DPM++ SDE Karras';
    case DDIM = 'DDIM';
    case PLMS = 'PLMS';
    case UNIPC = 'UniPC';
}
