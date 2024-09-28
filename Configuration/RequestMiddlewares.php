<?php

use JambageCom\Taxajax\Middleware\XajaxHandler;

// EXT:taxajax/Configuration/RequestMiddlewares.php
return [
    'frontend' => [
        'jambagecom/taxajax/preprocessing' => [
            'target' =>  XajaxHandler::class,
            'description' => 'The Ajax calls will be processed and no page will be generated.',
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering'
            ],
            'before' => [
                'typo3/cms-frontend/content-length-headers',
            ],
        ],
    ],
];

