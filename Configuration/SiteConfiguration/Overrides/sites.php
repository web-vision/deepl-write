<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use WebVision\DeeplWrite\Form\UserFunc\WriteSupport;

// @todo as this logic in between the SiteConfiguration is getting more complex,
//       we should add a validator checking for invalid combinations saved into the site config

(static function (): void {
    $GLOBALS['SiteConfiguration']['site_language']['columns']['isWritingStyleOptimized'] = [
        'label' => 'Is a writing style optimized language',
        'onChange' => 'reload',
        'displayCond' => [
            'AND' => [
                'USER:' . WriteSupport::class . '->languageIsRephraseSupported',
            ]
        ],
        'config' => [
            'type' => 'check',
        ],
    ];
    if (!ExtensionManagementUtility::isLoaded('deepltranslate_core')) {
        $GLOBALS['SiteConfiguration']['site_language']['columns']['deeplTargetLanguage'] = [
            'label' => 'DeepL Target language',
            'description' => 'The language the write optimization should be done in.',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => WriteSupport::class . '->getSupportedLanguageForField',
                'items' => [],
                'minitems' => 0,
                'maxitems' => 1,
                'size' => 1,
            ],
        ];
    }

    $GLOBALS['SiteConfiguration']['site_language']['columns']['deeplWriteTone'] = [
        'label' => 'DeepL Write tone',
        'onChange' => 'reload',
        'displayCond' => [
            'AND' => [
                'FIELD:isWritingStyleOptimized:REQ:true',
                'FIELD:deeplWriteWritingStyle:REQ:false',
            ],
        ],
        'description' => 'The language the write optimization should be done in.',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'itemsProcFunc' => WriteSupport::class . '->getSupportedToneForField',
            'items' => [
                [
                    'label' => '-- Choose style OR tone --',
                    'value' => '',
                ]
            ],
            'minitems' => 0,
            'maxitems' => 1,
            'size' => 1,
        ],
    ];

    $GLOBALS['SiteConfiguration']['site_language']['columns']['deeplWriteWritingStyle'] = [
        'label' => 'DeepL Write writing style',
        'description' => 'The language the write optimization should be done in.',
        'onChange' => 'reload',
        'displayCond' => [
            'AND' => [
                'FIELD:isWritingStyleOptimized:REQ:true',
                'FIELD:deeplWriteTone:REQ:false',
            ],
        ],
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'itemsProcFunc' => WriteSupport::class . '->getSupportedWritingStyleForField',
            'items' => [
                [
                    'label' => '-- Choose style OR tone --',
                    'value' => '',
                ]
            ],
            'minitems' => 0,
            'maxitems' => 1,
            'size' => 1,
        ],
    ];

    $deeplPaletteFields = [
        'deeplTargetLanguage',
        'isWritingStyleOptimized',
        '--linebreak--',
        'deeplWriteWritingStyle',
        'deeplWriteTone',
    ];

    if (ExtensionManagementUtility::isLoaded('deepltranslate_core')) {
        $deeplPaletteFields[] = 'deeplFormality';
        $GLOBALS['SiteConfiguration']['site_language']['columns']['deeplFormality']['displayCond']['AND'][] = 'FIELD:isWritingStyleOptimized:REQ:false';
    }
    $GLOBALS['SiteConfiguration']['site_language']['palettes']['deepl'] = [
        'showitem' => implode(',', $deeplPaletteFields),
    ];
})();
