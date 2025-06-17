<?php

return [
    'dependencies' => ['backend'],
    'tags' => [
        'backend.form',
    ],
    'imports' => [
        '@web-vision/deepl-write/deeplwrite-plugin.js' => 'EXT:deepl_write/Resources/Public/JavaScript/Ckeditor/deeplwrite-plugin.js',
    ],
];
