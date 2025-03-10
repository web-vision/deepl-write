<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Form\UserFunc;

use TYPO3\CMS\Backend\Form\FormDataProvider\EvaluateDisplayConditions;
use WebVision\DeeplWrite\Domain\Enum\RephraseSupportedDeepLLanguage;
use WebVision\DeeplWrite\Domain\Enum\RephraseToneDeepL;
use WebVision\DeeplWrite\Domain\Enum\RephraseWritingStyleDeepL;

final class WriteSupport
{
    public function getSupportedLanguageForField(array &$configuration): void
    {
        foreach (RephraseSupportedDeepLLanguage::cases() as $supportedLanguage) {
            $configuration['items'][] = [
                'label' => $supportedLanguage->value,
                'value' => $supportedLanguage->value
            ];
        }
    }

    public function getSupportedToneForField(array &$configuration): void
    {
        foreach (RephraseToneDeepL::cases() as $supportedTone) {
            $configuration['items'][] = [
                'label' => $supportedTone->value,
                'value' => $supportedTone->value
            ];
        }
    }

    public function getSupportedWritingStyleForField(array &$configuration): void
    {
        foreach (RephraseWritingStyleDeepL::cases() as $supportedWritingStyle) {
            $configuration['items'][] = [
                'label' => $supportedWritingStyle->value,
                'value' => $supportedWritingStyle->value
            ];
        }
    }

    /**
     * @param array{record?: array{deeplTargetLanguage?: array<int, string>|string|null}} $params
     */
    public function languageIsRephraseSupported(array $params, EvaluateDisplayConditions $conditions): bool
    {
        if (!isset($params['record'])) {
            return false;
        }

        $record = $params['record'];
        if (!isset($record['deeplWriteLanguage'])) {
            return false;
        }

        $setUpTargetLanguage = $record['deeplWriteLanguage'];

        if ($setUpTargetLanguage === []) {
            return false;
        }
        $languageSupport = RephraseSupportedDeepLLanguage::tryFrom(is_array($setUpTargetLanguage) ? array_pop($setUpTargetLanguage) : $setUpTargetLanguage);
        return $languageSupport instanceof RephraseSupportedDeepLLanguage;
    }
}
