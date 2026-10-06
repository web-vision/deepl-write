<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability\Calculator;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use WebVision\DeeplWrite\Readability\Result\ReadabilityResult;

/**
 * This class is an implementation generating the Flesch Reading Ease score for German.
 * It calculates as follows:
 *
 * FRE = 180 - (Average sentence Length (ASL)) - (58.5 * Average word length (AWL))
 *
 * ASL = (number of words) / (number of sentences)
 * ASW = (number of syllables) / (number of words)
 *
 * The corresponding score is between 0 and 100, where
 *  * 0 means really difficult to read
 *  * 100 means really easy to read
 *
 * For a better overview of the different scoring levels,
 * @see https://en.wikipedia.org/wiki/Flesch%E2%80%93Kincaid_readability_tests#Flesch_reading_ease
 *
 * For German calculation,
 * @see https://de.wikipedia.org/wiki/Lesbarkeitsindex#F%C3%BCr_Deutsch
 */
#[AsTaggedItem('deepl.readability')]
final class FleschKincaidGerman extends AbstractReadabilityCalculator
{
    protected const LANGUAGE = 'de';
    public function calculateReadability(string $text): ReadabilityResult
    {
        $sentences = $this->countSentences($text);
        $words = $this->countWords($text);
        $syllables = $this->countSyllables($text);
        $characters = $this->countCharacters($text);
        return new ReadabilityResult(
            $text,
            $sentences,
            $words,
            $syllables,
            $characters,
            $this->calculateScore($words, $sentences, $syllables)
        );
    }

    private function calculateScore(
        int $words,
        int $sentences,
        int $syllables
    ): float {
        if ($sentences <= 0) {
            $sentences = 1;
        }
        if ($words <= 0) {
            throw new \InvalidArgumentException(
                'The number of words can not be negative or zero!',
                1757679534
            );
        }
        return 180 - ($words/$sentences) - (58.5 * $syllables/$words);
    }
}
