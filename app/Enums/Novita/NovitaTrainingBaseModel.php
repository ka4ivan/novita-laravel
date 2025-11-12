<?php

declare(strict_types=1);

namespace App\Enums\Novita;

enum NovitaTrainingBaseModel: string
{
    case STABLE_DIFFUSION_XL_BASE_1_0 = 'stable-diffusion-xl-base-1.0';
    case DREAMSHAPERXL09ALPHA_ALPHA2XL10_91562 = 'dreamshaperXL09Alpha_alpha2Xl10_91562';
    case PROTOVISIONXLHIGHFIDELITY3D_RELEASE0630BAKEDVAE_154359 = 'protovisionXLHighFidelity3D_release0630Bakedvae_154359';
    case V1_5_PRUNED_EMAONLY = 'v1-5-pruned-emaonly';
    case EPICREALISM_NATURALSIN_121250 = 'epicrealism_naturalSin_121250';
    case CHILLOUTMIX_NIPRUNEDFP32FIX = 'chilloutmix_NiPrunedFp32Fix';
    case ABYSSORANGEMIX3AOM3_AOM3A3_10864 = 'abyssorangemix3AOM3_aom3a3_10864';
    case DREAMSHAPER_8_93211 = 'dreamshaper_8_93211';
    case WFCHILD_V1_0 = 'WFChild_v1.0';
    case MAJICHENMIXREALISTIC_V10 = 'majichenmixrealistic_v10';
    case REALISTICVISIONV51_V51VAE_94301 = 'realisticVisionV51_v51VAE_94301';
    case SDXLUNSTABLEDIFFUSERS_V11_216694 = 'sdxlUnstableDiffusers_v11_216694';
    case REALISTICVISIONV40_V40VAE_81510 = 'realisticVisionV40_v40VAE_81510';
    case EPICREALISMXL_V10_247189 = 'epicrealismXL_v10_247189';
    case SOMBOY_V10_172675 = 'somboy_v10_172675';
    case YESMIXXL_V10_283329 = 'yesmixXL_v10_283329';
    case ANIMAGINEXLV31_V31_325600 = 'animagineXLV31_v31_325600';
}
