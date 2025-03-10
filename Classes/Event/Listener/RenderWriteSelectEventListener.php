<?php

declare(strict_types=1);

namespace WebVision\DeeplWrite\Event\Listener;

use TYPO3\CMS\Backend\Controller\Event\RenderAdditionalContentToRecordListEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Site\Entity\Site;
use WebVision\DeeplWrite\Generator\WriteDropdownGenerator;

#[AsEventListener(
    identifier: 'deeplWrite/render-select'
)]
final class RenderWriteSelectEventListener
{
    public function __construct(
        private readonly WriteDropdownGenerator $writeDropdownGenerator,
    ) {
    }

    public function __invoke(RenderAdditionalContentToRecordListEvent $event): void
    {
        $request = $event->getRequest();
        /** @var Site $site */
        $site = $request->getAttribute('site');
        $siteLanguages = $site->getLanguages();
        $options = $this->writeDropdownGenerator->buildWriteDropdown(
            $siteLanguages,
            (int)($request->getQueryParams()['id'] ?? 0),
            $request->getUri()
        );

        if ($options === '') {
            return;
        }
        $additionalHeader = '<div class="form-row">'
            . '<div class="form-group">'
            . '<select class="form-select" name="createNewLanguage" data-global-event="change" data-action-navigate="$value">'
            . $options
            . '</select>'
            . '</div>'
            . '</div>';
        $event->addContentAbove($additionalHeader);
    }
}
