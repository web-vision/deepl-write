<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'DeepL Write',
    'description' => 'DeepL Write support for TYPO3. Write better texts, translate to simple language',
    'category' => 'backend',
    'author' => 'web-vision GmbH Team',
    'author_company' => 'web-vision GmbH',
    'author_email' => 'hello@web-vision.de',
    'state' => 'stable',
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.4.99',
            'typo3' => '12.4.0-13.4.99',
            'backend' => '12.4.0-13.4.99',
            'deepl_base' => '*',
            'deeplcom_deepl_php' => '1.12.0-1.12.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'container' => '*',
            'dashboard' => '*',
            'install' => '*',
            'enable_translated_content' => '*',
            'deepltranslate_core' => '*',
        ],
    ],
];
