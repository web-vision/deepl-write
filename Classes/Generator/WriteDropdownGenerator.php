<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Generator;

use Doctrine\DBAL\Exception;
use Psr\Http\Message\UriInterface;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\WorkspaceRestriction;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebVision\DeeplWrite\Domain\Enum\RephraseSupportedDeepLLanguage;

final class WriteDropdownGenerator
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
    )
    {
    }

    /**
     * @param iterable<SiteLanguage> $siteLanguages
     */
    public function buildWriteDropdown(
        iterable $siteLanguages,
        int $currentPageId,
        string|UriInterface $requestUri
    ): string {
        if ($currentPageId <= 0) {
            return '';
        }

        $availableTranslations = [];
        $foundTranslations = $this->getCurrentPageTranslations($currentPageId);
        foreach ($siteLanguages as $language) {
            $languageConfiguration = $language->toArray();
            if (
                ($languageConfiguration['deeplWriteLanguage'] ?? '') !== ''
                && !array_key_exists($language->getLanguageId(), $foundTranslations)
            ) {
                $writeAvailableLanguage = RephraseSupportedDeepLLanguage::tryFrom($languageConfiguration['deeplWriteLanguage']);
                if (!$writeAvailableLanguage instanceof RephraseSupportedDeepLLanguage) {
                    continue;
                }
                $availableTranslations[$language->getLanguageId()] = sprintf(
                    '%s: %s',
                    $language->getTitle(),
                    (($languageConfiguration['deeplWriteTone'] ?? false) ?: ($languageConfiguration['deeplWriteWritingStyle'] ?? false) ?: '')
                );
            }
        }

        if ($availableTranslations === []) {
            return '';
        }

        $output = '';
        foreach ($availableTranslations as $languageUid => $languageTitle) {
            $parameters = [
                'justLocalized' => 'pages:' . $currentPageId . ':' . $languageUid,
                'returnUrl' => (string)$requestUri,
            ];
            $redirectUrl = $this->buildBackendRoute('record_edit', $parameters);
            $params = [];
            $params['redirect'] = $redirectUrl;
            $params['cmd']['pages'][$currentPageId]['deeplwrite'] = $languageUid;
            $targetUrl = $this->buildBackendRoute('tce_db', $params);
            $output .= '<option value="' . htmlspecialchars($targetUrl) . '">' . htmlspecialchars($languageTitle) . '</option>';
        }

        if ($output === '') {
            return '';
        }

        return sprintf(
            '<option value="">%s</option>%s',
            htmlspecialchars($this->getLocalization()->sL('LLL:EXT:deepl_write/Resources/Private/Language/locallang.xlf:backend.label')),
            $output
        );
    }

    /**
     * Returns the translated pages as languageId => pageIdOfTranslation
     * @return array<positive-int, positive-int>
     * @throws Exception
     */
    private function getCurrentPageTranslations(int $pageUid): array
    {
        $localizationParentField = $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'];
        $languageField = $GLOBALS['TCA']['pages']['ctrl']['languageField'];
        $queryBuilder = $this->connectionPool
            ->getQueryBuilderForTable('pages');
        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(
                GeneralUtility::makeInstance(
                    WorkspaceRestriction::class,
                    (int)$this->getBackendUser()?->workspace
                )
            );
        $query = $queryBuilder
            ->select('uid', $languageField)
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq(
                    $localizationParentField,
                    $queryBuilder->createNamedParameter($pageUid, Connection::PARAM_INT)
                )
            )
            ->executeQuery();
        $foundTranslations = [
            0 => $pageUid,
        ];
        while ($row = $query->fetchAssociative()) {
            $foundTranslations[$row[$languageField]] = $row['uid'];
        }
        return $foundTranslations;
    }

    private function getBackendUser(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'] ?? null;
    }

    private function getLocalization(): LanguageService
    {
        return GeneralUtility::makeInstance(LanguageServiceFactory::class)
            ->createFromUserPreferences($this->getBackendUser());
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
