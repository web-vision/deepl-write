<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability\Calculator;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Flesch reading-ease score for English, using the original Flesch (1948)
 * formula:
 *
 * FRE = 206.835 - (1.015 * ASL) - (84.6 * ASW)
 *
 * ASL = number of words / number of sentences
 * ASW = number of syllables / number of words
 *
 * The score ranges from 0 (really difficult to read) to 100 (really easy to
 * read).
 *
 * @see https://en.wikipedia.org/wiki/Flesch%E2%80%93Kincaid_readability_tests#Flesch_reading_ease
 */
#[AutoconfigureTag('deepl.readability')]
final class FleschKincaidEnglish extends AbstractReadabilityCalculator
{
    protected const LANGUAGE = 'en-us';
    protected const HYPHENATION_LOCALE = 'en_US';

    protected function calculateScore(float $averageSentenceLength, float $averageSyllablesPerWord): float
    {
        return 206.835 - 1.015 * $averageSentenceLength - 84.6 * $averageSyllablesPerWord;
    }
}
