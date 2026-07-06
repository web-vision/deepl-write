<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability\Calculator;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Flesch reading-ease score for Italian, using the Franchina & Vacca (1972)
 * adaptation of the original Flesch formula:
 *
 * FRE = 206 - W - (0.65 * S)
 *
 * W = number of words / number of sentences (ASL)
 * S = number of syllables per 100 words (= 100 * ASW)
 *
 * Expressed with the average syllables per word (ASW) used here, the syllable
 * term becomes 0.65 * 100 * ASW = 65 * ASW.
 *
 * The score ranges from 0 (really difficult to read) to 100 (really easy to
 * read).
 *
 * @see https://it.wikipedia.org/wiki/Formula_di_Flesch
 * @see https://www.okpedia.it/indice_di_flesch
 */
#[AutoconfigureTag('deepl.readability')]
final class FleschKincaidItalian extends AbstractReadabilityCalculator
{
    protected const LANGUAGE = 'it';
    protected const HYPHENATION_LOCALE = 'it_IT';

    protected function calculateScore(float $averageSentenceLength, float $averageSyllablesPerWord): float
    {
        return 206.0 - $averageSentenceLength - 65.0 * $averageSyllablesPerWord;
    }
}
