<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability\Calculator;

use Org\Heigl\Hyphenator\Hyphenator;
use WebVision\DeeplWrite\Readability\ReadabilityCalculatorInterface;
use WebVision\DeeplWrite\Readability\Result\ReadabilityResult;

/**
 * Base class for the Flesch reading-ease calculators.
 *
 * It provides the shared text metrics (sentences, words, syllables and
 * characters) and the overall calculation flow. Concrete calculators only need
 * to declare the language they serve, the hyphenation locale used for syllable
 * counting and the language specific Flesch reading-ease formula in
 * {@see self::calculateScore()}.
 */
abstract class AbstractReadabilityCalculator implements ReadabilityCalculatorInterface
{
    /**
     * Language tag (matched by its primary subtag) this calculator serves.
     */
    protected const LANGUAGE = 'not-supported';

    /**
     * Locale used to load the org_heigl/hyphenator hyphenation patterns for
     * syllable counting. Must match a dictionary shipped by the library, e.g.
     * "de_DE", "en_US", "fr", "it_IT" or "pt_BR".
     */
    protected const HYPHENATION_LOCALE = 'en_US';

    protected const SENTENCE_SPLIT = '/([!\.\?] )/';
    protected const HYPHENATED_SPLIT = '/([(\s)+!\.\?|])/';

    final public function calculateReadability(string $text): ReadabilityResult
    {
        $sentences = $this->countSentences($text);
        $words = $this->countWords($text);
        $syllables = $this->countSyllables($text);
        $characters = $this->countCharacters($text);

        if ($words <= 0) {
            throw new \InvalidArgumentException(
                'Readability can not be calculated for a text without countable words.',
                1783350001
            );
        }
        if ($sentences <= 0) {
            $sentences = 1;
        }

        // Very short or very simple texts can push the formula above 100. As
        // 100 is the defined maximum ("very easy to read") the score is capped
        // here; this is a known property of the Flesch formulas and acceptable
        // for the quick overview this feature provides.
        $score = min(
            $this->calculateScore($words / $sentences, $syllables / $words),
            100.0,
        );

        return new ReadabilityResult($text, $sentences, $words, $syllables, $characters, $score);
    }

    /**
     * Calculate the language specific Flesch reading-ease score.
     *
     * @param float $averageSentenceLength number of words / number of sentences (ASL)
     * @param float $averageSyllablesPerWord number of syllables / number of words (ASW)
     */
    abstract protected function calculateScore(float $averageSentenceLength, float $averageSyllablesPerWord): float;

    final protected function countSentences(string $text): int
    {
        $sentences = preg_split(self::SENTENCE_SPLIT, $text);
        if ($sentences === false) {
            return 0;
        }
        return count($sentences);
    }

    protected function countWords(string $text): int
    {
        // Count words as sequences of unicode letters, keeping intra-word
        // apostrophes and hyphens together (e.g. "don't", "arc-en-ciel").
        // Unlike str_word_count() this handles the accented characters used by
        // languages such as French, Italian, Portuguese, Spanish and German.
        return (int)preg_match_all('/\p{L}+(?:[\x27\x{2019}-]\p{L}+)*/u', $text);
    }

    final protected function countSyllables(string $text): int
    {
        $hyphenator = new Hyphenator();
        $options = $hyphenator->getOptions();
        $options->setDefaultLocale(static::HYPHENATION_LOCALE);
        $options->setHyphen('|');
        $result = $hyphenator->hyphenate($text);
        if (!is_string($result)) {
            return 0;
        }
        $splitted = preg_split(self::HYPHENATED_SPLIT, $result);
        if ($splitted === false) {
            return 0;
        }
        return count($splitted);
    }

    protected function countCharacters(string $text): int
    {
        return mb_strlen($text);
    }

    public function getLanguage(): string
    {
        return static::LANGUAGE;
    }
}
