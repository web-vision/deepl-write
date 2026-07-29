<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'DeepL Write',
    'description' => 'DeepL Write support for TYPO3. Write better texts, translate to simple language',
    'version' => '1.0.5',
    'category' => 'misc',
    'state' => 'stable',
    'author' => 'web-vision GmbH Team',
    'author_email' => 'hello@web-vision.de',
    'author_company' => 'web-vision GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
            'backend' => '12.4.0-13.4.99',
            'php' => '8.1.0-8.5.99',
            'deepl_base' => '1.0.4-1.99.99',
            'deeplcom_deepl_php' => '1.19.0-1.19.99',
        ],
        'suggests' => [
            'container' => '',
            'dashboard' => '',
            'install' => '',
            'enable_translated_content' => '',
            'deepltranslate_core' => '',
        ],
    ],
];
