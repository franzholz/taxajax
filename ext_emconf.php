<?php

########################################################################
# Extension Manager/Repository config file for ext "taxajax".
########################################################################

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 adapted xaJax',
    'description' => 'Enhancement to the xajax extension with TYPO3 specific code.',
    'category' => 'misc',
    'version' => '0.7.2',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'author' => 'Jared White, J. Max Wilson, Franz Holzinger',
    'author_email' => 'franz@ttproducts.de',
    'author_company' => 'jambage.com',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-8.1.99',
            'typo3' => '10.4.0-11.5.99',
        ],
        'suggests' => [
        ],
    ],
];

