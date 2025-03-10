<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Service;

use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use WebVision\DeeplWrite\Domain\Enum\RephraseSupportedDeepLLanguage;

final class LanguageService
{
    public function getTargetLanguageForRephrasing(SiteInterface $site, int $targetLanguage): ?RephraseSupportedDeepLLanguage
    {
        $targetLanguage = $site->getLanguageById($targetLanguage);
        $targetLanguageConfiguration = $targetLanguage->toArray();
        $deeplWriteLanguage = $targetLanguageConfiguration['deeplWriteLanguage'];

        return RephraseSupportedDeepLLanguage::tryFrom($deeplWriteLanguage);
    }
}
