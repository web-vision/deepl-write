<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'DeepL Write',
    'description' => 'DeepL Write support for TYPO3. Write better texts, translate to simple language',
    'version' => '2.0.2',
    'category' => 'misc',
    'state' => 'stable',
    'author' => 'web-vision GmbH Team',
    'author_email' => 'hello@web-vision.de',
    'author_company' => 'web-vision GmbH',
    'constraints' => [
        'depends' => [
            'deeplcom_deepl_php' => '1.19.0-1.19.99',
            'php' => '8.2.0-8.5.99',
            'typo3' => '13.4.0-14.3.99',
            'backend' => '13.4.0-14.3.99',
            'deepl_base' => '2.0.4-2.99.99',
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
