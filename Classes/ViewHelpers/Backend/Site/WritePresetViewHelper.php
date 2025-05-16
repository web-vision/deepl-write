<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\ViewHelpers\Backend\Site;

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use WebVision\DeeplWrite\Domain\Enum\RephraseSupportedDeepLLanguage;
use WebVision\DeeplWrite\Domain\Enum\RephraseToneDeepL;

final class WritePresetViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;
    protected $escapeChildren = false;

    public function __construct(
        private readonly SiteFinder $siteFinder,
    ) {
    }

    public function initializeArguments()
    {
        $this->registerArgument('siteLanguageIds', 'string', 'Comma separated site languages', true);
    }

    public function render(): string
    {
        $siteLanguageIds = GeneralUtility::intExplode(',', $this->arguments['siteLanguageIds'], true);
        $request = $this->renderingContext->getRequest();
        if (!$request instanceof ServerRequest) {
            return '';
        }
        $queryParams = $request->getQueryParams();
        if (!array_key_exists('site', $queryParams)) {
            return '';
        }
        $siteIdentifier = $queryParams['site'];

        try {
            $site = $this->siteFinder->getSiteByIdentifier($siteIdentifier);
        } catch (SiteNotFoundException $e) {
            return '';
        }

        $deeplWritePossibleLanguages = [];
        $deeplWriteConfiguredLanguages = [];
        foreach ($site->getLanguages() as $language) {
            if (($language->toArray()['deeplWriteLanguage'] ?? '') !== '') {
                $deeplWriteConfiguredLanguages[$language->getLocale()->getLanguageCode()][] = $language;
                continue;
            }
            $languageCode = strtoupper($language->getLocale()->getName());

            if (!RephraseSupportedDeepLLanguage::isLanguageSupported($languageCode)) {
                $languageCode = strtoupper($language->getLocale()->getLanguageCode());
                if (!RephraseSupportedDeepLLanguage::isLanguageSupported($languageCode)) {
                    continue;
                }
            }
            if (!in_array($language, $deeplWriteConfiguredLanguages)) {
                $deeplWritePossibleLanguages[] = $language;
            }
        }

        $out = '<select id="deeplWritePreset" class="form-select d-inline-block w-auto">';
        $out .= '<option value="" selected>DeepL Write Preset...</option>';
        foreach ($deeplWritePossibleLanguages as $language) {
            $out .= '<optgroup label="' . $language->getLocale()->getLanguageCode() . '">';

            foreach (RephraseToneDeepL::cases() as $rephraseToneDeepL) {
                $out .= '<option value="{languageId: ' . $language->getLanguageId() . ', tone: \'' . $rephraseToneDeepL->value . '\'}">' . $language->getTitle() . ': ' . $rephraseToneDeepL->value . '</option>';
            }
            $out .= '</optgroup>';
        }
        $out .= '</select>';

        return $out;
    }
}
