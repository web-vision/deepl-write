<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability\Calculator;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Flesch reading-ease score for German, using the Amstad (1978) adaptation of
 * the original Flesch formula:
 *
 * FRE = 180 - ASL - (58.5 * ASW)
 *
 * ASL = number of words / number of sentences
 * ASW = number of syllables / number of words
 *
 * The score ranges from 0 (really difficult to read) to 100 (really easy to
 * read).
 *
 * @see https://en.wikipedia.org/wiki/Flesch%E2%80%93Kincaid_readability_tests#Flesch_reading_ease
 * @see https://de.wikipedia.org/wiki/Lesbarkeitsindex#F%C3%BCr_Deutsch
 */
#[AutoconfigureTag('deepl.readability')]
final class FleschKincaidGerman extends AbstractReadabilityCalculator
{
    protected const LANGUAGE = 'de';
    protected const HYPHENATION_LOCALE = 'de_DE';

    protected function calculateScore(float $averageSentenceLength, float $averageSyllablesPerWord): float
    {
        return 180.0 - $averageSentenceLength - 58.5 * $averageSyllablesPerWord;
    }
}
