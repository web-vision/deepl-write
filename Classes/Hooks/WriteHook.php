<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Hooks;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\DeeplWrite\Domain\Enum\RephraseToneDeepL;
use WebVision\DeeplWrite\Domain\Enum\RephraseWritingStyleDeepL;
use WebVision\DeeplWrite\FieldType\FieldTypeRegistry;
use WebVision\DeeplWrite\Service\DeeplService;
use WebVision\DeeplWrite\Service\LanguageService;

final class WriteHook
{
    public function __construct(
        private readonly DeeplService $deeplService,
        private readonly LanguageService $languageService,
        private readonly SiteFinder $siteFinder,
    ) {
    }

    /**
     * @param string $id
     * @param mixed $value
     * @param int $pasteUpdate
     */
    public function processCmdmap(
        string $command,
        string $table,
               $id,
               $value,
        bool &$commandIsProcessed,
        DataHandler $dataHandler,
        $pasteUpdate
    ): void {
        if ($command !== 'deeplwrite') {
            return;
        }

        $recordId = $dataHandler->localize($table, $id, $value);
        // localization went wrong
        if ($recordId === false) {
            return;
        }

        $originalRecord = BackendUtility::getRecord($table, $id);
        $translatedRecord = BackendUtility::getRecordLocalization($table, $id, $value);

        if ($translatedRecord === null) {
            return;
        }

        $this->processRecordFieldsAndUpdate(
            $table,
            array_pop($translatedRecord),
            $originalRecord,
            $value
        );

        $commandIsProcessed = true;
    }

    private function processRecordFieldsAndUpdate(string $table, array $translatedRecord, array $originalRecord, int|string $languageId): void
    {
        $pid = ($table === 'pages') ? $originalRecord['uid'] : $originalRecord['pid'];
        $site = $this->siteFinder->getSiteByPageId($pid);
        $updateFields = [];

        $language = $this->languageService->getTargetLanguageForRephrasing($site, (int)$languageId);
        if ($language === null) {
            return;
        }

        $tca = $GLOBALS['TCA'][$table]['columns'];

        $detectedType = null;
        $typeField = $GLOBALS['TCA'][$table]['ctrl']['type'] ?? null;
        if ($typeField !== null) {
            $detectedType = $translatedRecord[$typeField] ?? null;
        }

        foreach ($translatedRecord as $field => $value) {
            if (!isset($tca[$field])) {
                continue;
            }
            if (($tca[$field]['l10n_mode'] ?? '') !== 'prefixLangTitle') {
                continue;
            }
            if ($value === null) {
                continue;
            }

            $processingClass = FieldTypeRegistry::getFieldProcessingTypeByRenderType(
                $tca[$field]['config'],
                $table,
                $field,
                (string)$detectedType
            );
            $processValues = $processingClass->getTextForProcessing($value);

            $rephrasedText = [];
            foreach ($processValues as $key => $text) {
                // @todo: this is a complete mess, should load configuration before and not here
                $rephrasedText[$key] = $this->deeplService->rephraseText(
                    $text,
                    $language,
                    RephraseWritingStyleDeepL::tryFrom($site->getLanguageById((int)$languageId)->toArray()['deeplWriteWritingStyle'] ?: ''),
                    RephraseToneDeepL::tryFrom($site->getLanguageById((int)$languageId)->toArray()['deeplWriteTone'] ?: ''),
                );
            }

            $rephrasedValue = $processingClass->getValueForDatabase($rephrasedText);

            $updateFields[$field] = $rephrasedValue;
        }

        if ($updateFields !== []) {
            $data = [
                $table => [
                    $translatedRecord['uid'] => $updateFields
                ],
            ];

            $internalDataHandler = GeneralUtility::makeInstance(DataHandler::class);
            $internalDataHandler->start($data, []);
            $internalDataHandler->process_datamap();
            if ($internalDataHandler->errorLog !== []) {
                // @todo handle errors while translation update
            }
        }
    }
}
