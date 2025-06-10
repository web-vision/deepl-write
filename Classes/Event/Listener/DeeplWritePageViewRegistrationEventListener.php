<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Event\Listener;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\View\PageLayoutContext;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\Deepl\Base\Event\ViewHelpers\ModifyInjectVariablesViewHelperEvent;
use WebVision\DeeplWrite\Domain\Enum\RephraseSupportedDeepLLanguage;

/**
 * This EventListener registers the partial and the dropdown options
 * for the translation/write optimization dropdown in PageView
 */
final class DeeplWritePageViewRegistrationEventListener
{
    public function __invoke(ModifyInjectVariablesViewHelperEvent $event): void
    {
        if ($event->getIdentifier() !== 'languageTranslationDropdown') {
            return;
        }
        $translationPartials = $event->getLocalVariableProvider()->get('translationPartials');
        if ($translationPartials === null) {
            $translationPartials = [];
        }
        $translationPartials[30] = 'Translation/WriteDropdown';
        $event->getLocalVariableProvider()->add('translationPartials', $translationPartials);

        $deeplWriteLanguages = [];
        $event->getLocalVariableProvider()->add('deeplWriteLanguages', []);
        /** @var PageLayoutContext|null $context */
        $context = $event->getGlobalVariableProvider()->get('context');
        if ($context === null) {
            return;
        }

        foreach ($context->getSiteLanguages() as $siteLanguage) {
            $languageConfiguration = $siteLanguage->toArray();
            if (($languageConfiguration['deeplWriteLanguage'] ?? '') !== '') {
                if (!RephraseSupportedDeepLLanguage::isLanguageSupported($languageConfiguration['deeplWriteLanguage'])) {
                    continue;
                }
                $deeplWriteLanguages[$siteLanguage->getTitle()] = $siteLanguage->getLanguageId();
            }
        }
        if ($deeplWriteLanguages === []) {
            return;
        }
        $options = [];
        foreach ($context->getNewLanguageOptions() as $key => $possibleLanguage) {
            if ($key === 0) {
                continue;
            }
            if (!array_key_exists($possibleLanguage, $deeplWriteLanguages)) {
                continue;
            }
            if (method_exists($context, 'getCurrentRequest')) {
                $request = $context->getCurrentRequest();
            } else {
                $request = $GLOBALS['TYPO3_REQUEST'];
            }
            $parameters = [
                'justLocalized' => 'pages:' . $context->getPageId() . ':' . $deeplWriteLanguages[$possibleLanguage],
                'returnUrl' => $request->getAttribute('normalizedParams')->getRequestUri(),
            ];

            $redirectUrl = $this->buildBackendRoute('record_edit', $parameters);
            $params = [];
            $params['redirect'] = $redirectUrl;
            $params['cmd']['pages'][$context->getPageId()]['deeplwrite'] = $deeplWriteLanguages[$possibleLanguage];

            $targetUrl = $this->buildBackendRoute('tce_db', $params);

            $options[$targetUrl] = sprintf(
                '%s: %s',
                $possibleLanguage,
                (($languageConfiguration['deeplWriteTone'] ?? false) ?: ($languageConfiguration['deeplWriteWritingStyle'] ?? false) ?: '')
            );
        }

        if ($options === []) {
            return;
        }
        $event->getLocalVariableProvider()->add('deeplWriteLanguages', $options);
    }

    /**
     * @throws RouteNotFoundException
     */
    private function buildBackendRoute(string $route, array $parameters): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute($route, $parameters);
    }
}
