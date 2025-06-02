<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use WebVision\DeeplWrite\Form\UserFunc\WriteSupport;

// @todo as this logic in between the SiteConfiguration is getting more complex,
//       we should add a validator checking for invalid combinations saved into the site config

(static function (): void {
    $GLOBALS['SiteConfiguration']['site_language']['columns']['deeplWriteLanguage'] = [
        'label' => 'DeepL Write language',
        'description' => 'The language the write optimization should be done in.',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'itemsProcFunc' => WriteSupport::class . '->getSupportedLanguageForField',
            'items' => [
                [
                    'label' => '-- Choose a writing style language --',
                    'value' => '',
                ],
            ],
            'minitems' => 0,
            'maxitems' => 1,
            'size' => 1,
        ],
    ];

    $GLOBALS['SiteConfiguration']['site_language']['columns']['deeplWriteTone'] = [
        'label' => 'DeepL Write tone',
        'onChange' => 'reload',
        'displayCond' => [
            'AND' => [
                'FIELD:deeplWriteLanguage:!=:',
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
                ],
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
                'FIELD:deeplWriteLanguage:!=:',
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
                ],
            ],
            'minitems' => 0,
            'maxitems' => 1,
            'size' => 1,
        ],
    ];

    $deeplPaletteFields = [
        'deeplWriteLanguage',
        '--linebreak--',
        'deeplWriteWritingStyle',
        'deeplWriteTone',
    ];

    if (ExtensionManagementUtility::isLoaded('deepltranslate_core')) {
        // ensure disabled fields if DeepL Write Language and writing Style optimized is set
        $GLOBALS['SiteConfiguration']['site_language']['columns']['deeplFormality']['displayCond']['AND'][] = 'FIELD:deeplWriteLanguage:=:';
        $GLOBALS['SiteConfiguration']['site_language']['columns']['deeplTargetLanguage']['displayCond']['AND'][] = 'FIELD:deeplWriteLanguage:=:';
        $GLOBALS['SiteConfiguration']['site_language']['columns']['deeplWriteLanguage']['displayCond']['AND'][] = 'FIELD:deeplTargetLanguage:=:';
    }
    $GLOBALS['SiteConfiguration']['site_language']['palettes']['deeplWrite'] = [
        'showitem' => implode(',', $deeplPaletteFields),
    ];
    $GLOBALS['SiteConfiguration']['site_language']['types']['1']['showitem'] = str_replace(
        '--palette--;;default,',
        '--palette--;;default, --palette--;LLL:EXT:deepl_write/Resources/Private/Language/locallang.xlf:site_configuration.deeplwrite.title;deeplWrite,',
        $GLOBALS['SiteConfiguration']['site_language']['types']['1']['showitem']
    );
})();
