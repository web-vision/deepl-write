<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Domain\Enum;

/**
 * @todo Currently DeepL API only supports this set of languages for rephrasing.
 *       Refactor this and replace with DeepL API call and usage, after DeepL
 *       API adds an official endpoint detecting rephrase languages.
 */
final class RephraseSupportedDeepLLanguage
{
    private const LANGUAGES = [
        'EN-GB' => [
            'writing_style' => true,
            'tone' => true,
        ],
        'EN-US' => [
            'writing_style' => true,
            'tone' => true,
        ],
        'DE' => [
            'writing_style' => true,
            'tone' => true,
        ],
        'ES' => [
            'writing_style' => false,
            'tone' => false,
        ],
        'FR' => [
            'writing_style' => false,
            'tone' => false,
        ],
        'IT' => [
            'writing_style' => false,
            'tone' => false,
        ],
        'PT-PT' => [
            'writing_style' => false,
            'tone' => false,
        ],
        'PT-BR' => [
            'writing_style' => false,
            'tone' => false,
        ],
    ];

    public static function getAllLanguages(): array
    {
        return array_keys(self::LANGUAGES);
    }

    public static function isLanguageSupported(string $language): bool
    {
        return array_key_exists($language, self::LANGUAGES);
    }

    public static function tryFrom(string $language): ?string
    {
        return array_key_exists($language, self::LANGUAGES) ? $language : null;
    }

    public static function isWritingStyleSupported(string $language): bool
    {
        if (!array_key_exists($language, self::LANGUAGES)) {
            return false;
        }
        return self::LANGUAGES[$language]['writing_style'] ?? false;
    }

    public static function isToneSupportedByLanguage(string $language): bool
    {
        if (!array_key_exists($language, self::LANGUAGES)) {
            return false;
        }
        return self::LANGUAGES[$language]['tone'] ?? false;
    }
}
