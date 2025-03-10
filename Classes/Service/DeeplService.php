<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Service;

use DeepL\DeepLClient;
use DeepL\DeepLException;
use DeepL\RephraseTextOptions;
use DeepL\TranslatorOptions;
use TYPO3\CMS\Core\Http\Client\GuzzleClientFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\DeeplWrite\Configuration\ConfigurationInterface;
use WebVision\DeeplWrite\Domain\Enum\RephraseSupportedDeepLLanguage;
use WebVision\DeeplWrite\Domain\Enum\RephraseToneDeepL;
use WebVision\DeeplWrite\Domain\Enum\RephraseWritingStyleDeepL;
use WebVision\DeeplWrite\Exception\ApiKeyNotSetException;
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
    private ?DeepLClient $translator = null;

    public function __construct(
        private readonly ConfigurationInterface $configuration
    ) {
        if ($this->configuration->getApiKey() === '') {
            throw new ApiKeyNotSetException(
                'The DeepL API key is not set',
                1741343502
            );
        }
        $options[TranslatorOptions::HTTP_CLIENT] = GeneralUtility::makeInstance(GuzzleClientFactory::class)->getClient();
        $this->translator = new DeepLClient($this->configuration->getApiKey(), $options);
    }

    /**
     * @throws RephraseConfigurationException
     * @throws DeepLException
     */
    public function rephraseText(
        string                         $text,
        RephraseSupportedDeepLLanguage $targetLanguage,
        RephraseWritingStyleDeepL      $writingStyle = null,
        RephraseToneDeepL              $tone = null
    ): string {
        $options = [];
        if ($writingStyle !== null && $tone !== null) {
            throw new RephraseConfigurationException(
                'You can only set one of the options "writingStyle" or "tone"',
                1741344565
            );
        }
        if ($writingStyle instanceof RephraseWritingStyleDeepL) {
            $options[RephraseTextOptions::WRITING_STYLE] = $writingStyle->value;
        }
        if ($tone instanceof RephraseToneDeepL) {
            $options[RephraseTextOptions::TONE] = $tone->value;
        }

        $splittedText = $this->splitTextToMaxSize($text);

        $textResult = [];
        foreach ($splittedText as $text) {
            $textResult[] = $this->optimizeText($text, $targetLanguage, $options);
        }
        return implode(" ", $textResult);
    }

    /**
     * @param array{
     *     writing_style?: string,
     *     tone?: string
     * }|empty $options
     * @throws DeepLException
     */
    private function optimizeText(
        string                         $text,
        RephraseSupportedDeepLLanguage $targetLanguage,
        array                          $options = []
    ): string
    {
        $rephrased = $this->translator->rephraseText(
            $text,
            $targetLanguage->value,
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
            $completeSentence = $sentence.$delimiter;
            if (mb_strlen($textSnippet.$completeSentence, '8bit') <= 10000) {
                $textSnippet .= $completeSentence;
            } else {
                $snippets[] = $textSnippet;
                $textSnippet = $completeSentence;
            }
        }

        return $snippets;
    }
}
