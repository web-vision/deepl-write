<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability\Calculator;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Flesch reading-ease score for Portuguese, using the Martins et al. (1996)
 * adaptation of the original Flesch formula developed at USP São Carlos:
 *
 * FRE = 248.835 - (1.015 * ASL) - (84.6 * ASW)
 *
 * ASL = number of words / number of sentences
 * ASW = number of syllables / number of words
 *
 * The English coefficients are kept while the constant is raised, accounting
 * for Portuguese words being longer on average. The score ranges from 0
 * (really difficult to read) to 100 (really easy to read).
 *
 * @see https://en.wikipedia.org/wiki/Flesch%E2%80%93Kincaid_readability_tests#Flesch_reading_ease
 * @see https://www.linguamatica.com/index.php/linguamatica/article/view/44
 */
#[AutoconfigureTag('deepl.readability')]
final class FleschKincaidPortuguese extends AbstractReadabilityCalculator
{
    protected const LANGUAGE = 'pt';
    protected const HYPHENATION_LOCALE = 'pt_BR';

    protected function calculateScore(float $averageSentenceLength, float $averageSyllablesPerWord): float
    {
        return 248.835 - 1.015 * $averageSentenceLength - 84.6 * $averageSyllablesPerWord;
    }
}
