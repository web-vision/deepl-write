<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Readability\Result;

/**
 * Represents the result of a readability analysis performed on a given text.
 * It provides metrics such as sentence, word, syllable, and character counts,
 * as well as a calculated readability score and averages per sentence or word.
 */
final class ReadabilityResult implements \JsonSerializable
{
    public function __construct(
        public readonly string $text,
        public readonly int $sentences,
        public readonly int $words,
        public readonly int $syllables,
        public readonly int $characters,
        public readonly float $score
    ) {
    }

    public function getAverageWordsPerSentence(): float
    {
        return round($this->words/$this->sentences, 2);
    }

    public function getAverageSyllablesPerWord(): float
    {
        return round($this->syllables/$this->words, 2);
    }

    public function jsonSerialize(): array
    {
        return [
            'sentences' => $this->sentences,
            'words' => $this->words,
            'syllables' => $this->syllables,
            'characters' => $this->characters,
            'avgSyllables' => $this->getAverageSyllablesPerWord(),
            'avgWords' => $this->getAverageWordsPerSentence(),
            'score' => $this->score,
        ];
    }
}
