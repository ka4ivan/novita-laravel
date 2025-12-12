<?php

declare(strict_types=1);

namespace App\Enums\Novita;

enum NovitaModelType: string
{
    case Checkpoint = 'checkpoint';
    case LoRA = 'lora';
    case VAE = 'vae';
    case ControlNet = 'controlnet';
    case Upscaler = 'upscaler';
    case TextualInversion = 'textualinversion';
}
