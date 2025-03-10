<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Domain\Enum;

enum RephraseWritingStyleDeepL: string
{
    case DEFAULT = 'default';
    case SIMPLE = 'simple';
    case BUSINESS = 'business';
    case ACADEMIC = 'academic';
    case CASUAL = 'casual';
    case PREFER_SIMPLE = 'prefer_simple';
    case PREFER_BUSINESS = 'prefer_business';
    case PREFER_ACADEMIC = 'prefer_academic';
    case PREFER_CASUAL = 'prefer_casual';
}
