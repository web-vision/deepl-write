<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Form\UserFunc;

use TYPO3\CMS\Backend\Form\FormDataProvider\EvaluateDisplayConditions;
use WebVision\DeeplWrite\Domain\Enum\RephraseSupportedDeepLLanguage;
use WebVision\DeeplWrite\Domain\Enum\RephraseToneDeepL;
use WebVision\DeeplWrite\Domain\Enum\RephraseWritingStyleDeepL;

final class WriteSupport
{
    public function getSupportedLanguagesForField(array &$configuration): void
    {
        foreach (RephraseSupportedDeepLLanguage::cases() as $supportedLanguage) {
            $configuration['items'][] = [
                'label' => $supportedLanguage->value,
                'value' => $supportedLanguage->name
            ];
        }
    }

    public function getSupportedToneForField(array &$configuration): void
    {
        foreach (RephraseToneDeepL::cases() as $supportedTone) {
            $configuration['items'][] = [
                'label' => $supportedTone->value,
                'value' => $supportedTone->name
            ];
        }
    }

    public function getSupportedWritingStyleForField(array &$configuration): void
    {
        foreach (RephraseWritingStyleDeepL::cases() as $supportedWritingStyle) {
            $configuration['items'][] = [
                'label' => $supportedWritingStyle->value,
                'value' => $supportedWritingStyle->name
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
        if (!isset($record['deeplTargetLanguage'])) {
            return false;
        }

        $setUpTargetLanguage = $record['deeplTargetLanguage'];

        if ($setUpTargetLanguage === []) {
            return false;
        }
        $languageSupport = RephraseSupportedDeepLLanguage::tryFrom(array_pop($setUpTargetLanguage));
        return $languageSupport instanceof RephraseSupportedDeepLLanguage;
    }
}
