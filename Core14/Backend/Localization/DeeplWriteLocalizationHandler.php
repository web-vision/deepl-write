<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Core14\Backend\Localization;

use TYPO3\CMS\Backend\Domain\Repository\Localization\LocalizationRepository;
use TYPO3\CMS\Backend\Localization\Finisher\NoopLocalizationFinisher;
use TYPO3\CMS\Backend\Localization\Finisher\RedirectLocalizationFinisher;
use TYPO3\CMS\Backend\Localization\Finisher\ReloadLocalizationFinisher;
use TYPO3\CMS\Backend\Localization\LocalizationHandlerInterface;
use TYPO3\CMS\Backend\Localization\LocalizationInstructions;
use TYPO3\CMS\Backend\Localization\LocalizationMode;
use TYPO3\CMS\Backend\Localization\LocalizationResult;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Localization\LanguageService as CoreLanguageService;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\DeeplWrite\Configuration\ConfigurationInterface;
use WebVision\DeeplWrite\Service\LanguageService;

/**
 * Localization handler that plugs DeepL Write into the modernized TYPO3 v14
 * translation wizard. It localizes a record and rephrases the translatable
 * fields via the DeepL Write API by dispatching the custom "deeplwrite"
 * DataHandler command (see {@see \WebVision\DeeplWrite\Hooks\WriteHook}).
 *
 * The handler is only offered for the "translate" mode and only when the
 * target site language has a supported DeepL Write language configured and a
 * DeepL API key is available. It therefore works both standalone (with
 * EXT:deepl_base and EXT:deeplcom_deepl_php) and alongside
 * EXT:deepltranslate_core, whose API key is used as fallback.
 *
 * @internal The TYPO3 LocalizationHandlerInterface is marked @internal and may
 *           change before the v14 LTS release; this handler is not public API.
 */
final readonly class DeeplWriteLocalizationHandler implements LocalizationHandlerInterface
{
    public function __construct(
        private UriBuilder $uriBuilder,
        private LocalizationRepository $localizationRepository,
        private SiteFinder $siteFinder,
        private LanguageService $languageService,
        private ConfigurationInterface $configuration,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'deeplwrite';
    }

    public function getLabel(): string
    {
        return 'deepl_write.wizards.localization:handler.deeplwrite.label';
    }

    public function getDescription(): string
    {
        return 'deepl_write.wizards.localization:handler.deeplwrite.description';
    }

    public function getIconIdentifier(): string
    {
        return 'actions-localize-deepl-write';
    }

    public function isAvailable(LocalizationInstructions $instructions): bool
    {
        if ($instructions->mode !== LocalizationMode::TRANSLATE) {
            // DeepL Write creates connected translations only, no free-mode copies.
            return false;
        }
        if ($this->configuration->getApiKey() === '') {
            // Without a DeepL API key DeepL Write cannot be used.
            return false;
        }
        $site = $this->determineSite($instructions);
        if ($site === null) {
            // Site configuration could not be determined for the record.
            return false;
        }
        // Only available when the target site language has a supported DeepL
        // Write language configured in the site configuration.
        return $this->languageService->getTargetLanguageForRephrasing($site, $instructions->targetLanguageId) !== null;
    }

    public function processLocalization(LocalizationInstructions $instructions): LocalizationResult
    {
        return match ($instructions->mainRecordType) {
            // Handle pages with optional content selection.
            'pages' => $this->processPageLocalization($instructions->recordUid, $instructions->targetLanguageId, $instructions->additionalData),
            // Handle single record localization for other record types.
            default => $this->processSingleRecordLocalization($instructions->mainRecordType, $instructions->recordUid, $instructions->targetLanguageId),
        };
    }

    /**
     * Process single record localization (non-page records).
     */
    private function processSingleRecordLocalization(
        string $type,
        int $uid,
        int $targetLanguage
    ): LocalizationResult {
        // Validate that the record exists.
        $record = BackendUtility::getRecord($type, $uid);
        if (!$record) {
            return LocalizationResult::error(
                [
                    sprintf(
                        $this->getLanguageService()->sL('deepl_write.wizards.localization:error.recordNotFound'),
                        $uid,
                        $type
                    ),
                ]
            );
        }

        // Nothing to do when the translation already exists.
        $existingTranslation = $this->localizationRepository->getRecordTranslation($type, $uid, $targetLanguage);
        if ($existingTranslation !== null) {
            return LocalizationResult::success(
                new NoopLocalizationFinisher()
            );
        }

        $cmd = [
            $type => [
                $uid => [
                    'deeplwrite' => $targetLanguage,
                ],
            ],
        ];

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], $cmd);
        $dataHandler->process_cmdmap();

        if ($dataHandler->errorLog !== []) {
            return LocalizationResult::error($dataHandler->errorLog);
        }

        // Get the newly created record UID from DataHandler's copy mapping.
        $newUid = $dataHandler->copyMappingArray_merged[$type][$uid] ?? null;

        // If no UID was found in the copy mapping, look up the translated record.
        if ($newUid === null) {
            $translation = $this->localizationRepository->getRecordTranslation($type, $uid, $targetLanguage);
            $newUid = $translation?->getUid();
        }

        $redirectUrl = $newUid !== null ? $this->generateRedirectUrl($type, $newUid, $targetLanguage) : null;

        return LocalizationResult::success(
            $redirectUrl !== null
                ? new RedirectLocalizationFinisher($redirectUrl)
                : new ReloadLocalizationFinisher()
        );
    }

    /**
     * Process page localization including selected content elements.
     *
     * @param array{selectedRecordUids?: int[]} $additionalData
     */
    private function processPageLocalization(
        int $pageUid,
        int $targetLanguage,
        array $additionalData
    ): LocalizationResult {
        $selectedContent = $additionalData['selectedRecordUids'] ?? [];

        $cmd = [];

        // Step 1: Create the page translation if it does not exist yet.
        $pageTranslation = $this->localizationRepository->getPageTranslations($pageUid, [$targetLanguage], $this->getBackendUser()->workspace);
        if ($pageTranslation === []) {
            $cmd['pages'] = [
                $pageUid => [
                    'deeplwrite' => $targetLanguage,
                ],
            ];
        }

        // Step 2: Add the selected content elements to the command.
        if (!empty($selectedContent)) {
            $cmd['tt_content'] = [];
            foreach ($selectedContent as $contentUid) {
                $cmd['tt_content'][(int)$contentUid] = [
                    'deeplwrite' => $targetLanguage,
                ];
            }
        }

        // Nothing to do (page already exists, no content selected).
        if ($cmd === []) {
            return LocalizationResult::success(
                new NoopLocalizationFinisher()
            );
        }

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], $cmd);
        $dataHandler->process_cmdmap();

        if ($dataHandler->errorLog !== []) {
            return LocalizationResult::error($dataHandler->errorLog);
        }

        $redirectUrl = $this->generateRedirectUrl('pages', $pageUid, $targetLanguage);
        return LocalizationResult::success(
            $redirectUrl !== null
                ? new RedirectLocalizationFinisher($redirectUrl)
                : new ReloadLocalizationFinisher()
        );
    }

    /**
     * Generate the redirect URL based on record type.
     */
    private function generateRedirectUrl(string $type, int $uid, int $targetLanguage): ?string
    {
        if ($type === 'pages') {
            return (string)$this->uriBuilder->buildUriFromRoute('web_layout', [
                'id' => $uid,
                'languages' => [$targetLanguage],
            ]);
        }

        $record = BackendUtility::getRecord($type, $uid);
        if ($record && isset($record['pid'])) {
            $returnUrl = null;

            if ($type === 'sys_file_metadata') {
                // Build the return URL to the filelist module for file metadata.
                try {
                    $file = GeneralUtility::makeInstance(ResourceFactory::class)->getFileObject((int)$record['file']);
                    $parentFolder = $file->getParentFolder();
                    $returnUrl = (string)$this->uriBuilder->buildUriFromRoute(
                        'media_management',
                        ['id' => $parentFolder->getCombinedIdentifier()]
                    );
                } catch (\Exception) {
                    // File not found or inaccessible, fall back to the default return URL.
                }
            }

            if ($returnUrl === null) {
                $returnUrl = (string)$this->uriBuilder->buildUriFromRoute(
                    'web_layout',
                    [
                        'id' => $record['pid'],
                        'languages' => [$targetLanguage],
                    ]
                );
            }

            return (string)$this->uriBuilder->buildUriFromRoute('record_edit', [
                'edit' => [
                    $type => [
                        $uid => 'edit',
                    ],
                ],
                'returnUrl' => $returnUrl,
            ]);
        }

        return null;
    }

    private function determineSite(LocalizationInstructions $instructions): ?SiteInterface
    {
        $pageId = $instructions->recordUid;
        if ($instructions->mainRecordType !== 'pages') {
            $record = BackendUtility::getRecord($instructions->mainRecordType, $instructions->recordUid);
            if ($record === null) {
                return null;
            }
            $pageId = (int)($record['pid'] ?? 0);
        }

        try {
            return $this->siteFinder->getSiteByPageId($pageId);
        } catch (SiteNotFoundException) {
            return null;
        }
    }

    private function getLanguageService(): CoreLanguageService
    {
        return $GLOBALS['LANG'];
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
