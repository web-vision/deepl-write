<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability\Calculator;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use WebVision\DeeplWrite\Readability\Result\ReadabilityResult;

/**
 * This class is an implementation generating the Flesch Reading Ease score for German.
 * It calculates as follows:
 *
 * FRE = 206.835 - (1.015 * Average sentence Length (ASL)) - (84.6 * Average word length (AWL))
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
 */
#[AsTaggedItem('deepl.readability')]
final class FleschKincaidEnglish extends AbstractReadabilityCalculator
{
    protected const LANGUAGE = 'en-us';
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
                1757680362
            );
        }

        // Too easy sentences and short texts COULD result in calculating a value above 100. In this case
        // set the result to 100, as this is the maximum.
        // This is a known issue in this formula, but can be ignored for a quick overview, as
        // 100 means very easy to read.
        $fleschKincaid = 206.835 - 1.015 * ($words/$sentences) - (84.6 * $syllables/$words);
        return ($fleschKincaid <= 100.0) ? $fleschKincaid : 100.0;
    }
}
