<?php

declare(strict_types=1);

use WebVision\Deepl\Base\Imaging\IconProvider\DeeplBaseSvgIconProvider;

/**
 * Light/dark theme aware ("dualtone") action icon for the DeepL Write
 * localization handler. Rendered through EXT:deepl_base's
 * {@see DeeplBaseSvgIconProvider}, which emits an SVG `<use>` reference so the
 * icon follows the backend colour scheme via `currentColor`.
 */
return [
    'actions-localize-deepl-write' => [
        'provider' => DeeplBaseSvgIconProvider::class,
        'source' => 'EXT:deepl_write/Resources/Public/Icons/deepl-write-mode-aware.svg',
    ],
];
