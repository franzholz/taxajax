<?php

return [
    'frontend' => [
        'jambagecom/taxajax/preprocessing' => [
            'target' =>  \JambageCom\Taxajax\Middleware\XajaxHandler::class,
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

