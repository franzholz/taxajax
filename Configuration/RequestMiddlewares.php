<?php

use JambageCom\Taxajax\Middleware\XajaxHandler;
return [
    'frontend' => [
        'jambagecom/taxajax/preprocessing' => [
            'target' =>  XajaxHandler::class,
            'description' => 'The Ajax calls will be processed and no page will be generated.',
            'after' => [
                'typo3/cms-frontend/page-resolver'
            ],
            'before' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ]
        ]
    ]
];
