<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Domain\Enum;

/**
 * @todo Currently DeepL API only supports this set of languages for rephrasing.
 *       Refactor this and replace with DeepL API call and usage, after DeepL
 *       API adds an official endpoint detecting rephrase languages.
 */
enum RephraseSupportedDeepLLanguage: string
{
    case DE = 'DE';
    case EN_GB = 'EN-GB';
    case EN_US = 'EN-US';
}
