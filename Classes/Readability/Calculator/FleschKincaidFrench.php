<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability\Calculator;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Flesch reading-ease score for French, using the Kandel & Moles (1958)
 * adaptation of the original Flesch formula:
 *
 * FRE = 207 - (1.015 * ASL) - (73.6 * ASW)
 *
 * ASL = number of words / number of sentences
 * ASW = number of syllables / number of words
 *
 * The score ranges from 0 (really difficult to read) to 100 (really easy to
 * read).
 *
 * @see https://fr.wikipedia.org/wiki/Lisibilit%C3%A9#Formule_de_Kandel_et_Moles
 * @see https://www.mba-ks.com/analyse-lisibilite-flesch/
 */
#[AutoconfigureTag('deepl.readability')]
final class FleschKincaidFrench extends AbstractReadabilityCalculator
{
    protected const LANGUAGE = 'fr';
    protected const HYPHENATION_LOCALE = 'fr';

    protected function calculateScore(float $averageSentenceLength, float $averageSyllablesPerWord): float
    {
        return 207.0 - 1.015 * $averageSentenceLength - 73.6 * $averageSyllablesPerWord;
    }
}
