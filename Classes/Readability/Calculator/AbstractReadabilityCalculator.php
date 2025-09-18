<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability\Calculator;

use Org\Heigl\Hyphenator\Hyphenator;
use WebVision\DeeplWrite\Readability\ReadabilityCalculatorInterface;

abstract class AbstractReadabilityCalculator implements ReadabilityCalculatorInterface
{
    protected const LANGUAGE = 'not-supported';
    protected const SENTENCE_SPLIT = '/([!\.\?] )/';
    protected const HYPHENATED_SPLIT = '/([(\s)+!\.\?|])/';

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
        return str_word_count($text);
    }

    final protected function countSyllables(string $text): int
    {
        $hyphenator = new Hyphenator();
        $hyphenator->getOptions()->setHyphen('|');
        $result = $hyphenator->hyphenate($text);
        $splitted = preg_split(self::HYPHENATED_SPLIT, $result);
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
