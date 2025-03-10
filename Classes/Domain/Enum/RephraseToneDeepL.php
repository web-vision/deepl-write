<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Domain\Enum;

enum RephraseToneDeepL: string
{
    case DEFAULT = 'default';
    case ENTHUSIASTIC = 'enthusiastic';
    case FRIENDLY = 'friendly';
    case CONFIDENTIAL = 'confident';
    case DIPLOMATIC = 'diplomatic';
    case PREFER_ENTHUSIASTIC = 'prefer_enthusiastic';
    case PREFER_FRIENDLY = 'prefer_friendly';
    case PREFER_CONFIDENTIAL = 'prefer_confident';
    case PREFER_DIPLOMATIC = 'prefer_diplomatic';
}
