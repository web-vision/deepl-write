<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Service;

use DeepL\DeepLClient;
use DeepL\DeepLException;
use DeepL\RephraseTextOptions;
use WebVision\DeeplWrite\Client\ClientFactory;
use WebVision\DeeplWrite\Domain\Enum\RephraseSupportedDeepLLanguage;
use WebVision\DeeplWrite\Domain\Enum\RephraseToneDeepL;
use WebVision\DeeplWrite\Domain\Enum\RephraseWritingStyleDeepL;
use WebVision\DeeplWrite\Exception\RephraseConfigurationException;

/**
 * Wrapper class for dealing with DeepL API
 *
 * @internal This class is part of EXT:deepl_write and only for internal usage,
 * therefore, not public API.
 */
final class DeeplService
{
    private const SENTENCE_SPLIT = '/([.?!]\s)/';
    private ?DeepLClient $deeplClient;

    public function __construct(
        ClientFactory $clientFactory,
    ) {
        $this->deeplClient = $clientFactory->buildDeeplClient();
    }

    /**
     * @throws RephraseConfigurationException
     * @throws DeepLException
     */
    public function rephraseText(
        string $text,
        string $targetLanguage,
        ?RephraseWritingStyleDeepL $writingStyle = null,
        ?RephraseToneDeepL $tone = null
    ): string {
        $options = [];
        if ($writingStyle !== null && $tone !== null) {
            throw new RephraseConfigurationException(
                'You can only set one of the options "writingStyle" or "tone"',
                1741344565
            );
        }
        if ($writingStyle instanceof RephraseWritingStyleDeepL && RephraseSupportedDeepLLanguage::isWritingStyleSupported($targetLanguage)) {
            $options[RephraseTextOptions::WRITING_STYLE] = $writingStyle->value;
        }
        if ($tone instanceof RephraseToneDeepL && RephraseSupportedDeepLLanguage::isToneSupportedByLanguage($targetLanguage)) {
            $options[RephraseTextOptions::TONE] = $tone->value;
        }

        $splittedText = $this->splitTextToMaxSize($text);

        $textResult = [];
        foreach ($splittedText as $text) {
            $textResult[] = $this->optimizeText($text, $targetLanguage, $options);
        }
        return implode(' ', $textResult);
    }

    /**
     * @param array{
     *     writing_style?: string,
     *     tone?: string
     * }|empty $options
     * @throws DeepLException
     */
    private function optimizeText(
        string $text,
        string $targetLanguage,
        array $options = []
    ): string {
        $rephrased = $this->deeplClient->rephraseText(
            $text,
            $targetLanguage,
            $options
        );

        return (string)$rephrased;
    }

    /**
     * @return string[]
     */
    private function splitTextToMaxSize(string $text): array
    {
        $textSize = mb_strlen($text, '8bit');
        if ($textSize <= 10000) {
            return [$text];
        }

        $sentences = preg_split(self::SENTENCE_SPLIT, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $countResult = count($sentences);

        $snippets = [];
        $textSnippet = '';
        for ($i = 0; $i < $countResult; $i = $i+2) {
            $sentence = $sentences[$i];
            $delimiter = $sentences[$i+1];
            $completeSentence = $sentence . $delimiter;
            if (mb_strlen($textSnippet . $completeSentence, '8bit') <= 10000) {
                $textSnippet .= $completeSentence;
            } else {
                $snippets[] = $textSnippet;
                $textSnippet = $completeSentence;
            }
        }

        return $snippets;
    }
}
