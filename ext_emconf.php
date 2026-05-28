<?php

########################################################################
# Extension Manager/Repository config file for ext "taxajax".
########################################################################

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 adapted xaJax 0.2.4',
    'description' => 'Enhancement to the xajax extension with TYPO3 specific code.',
    'category' => 'misc',
    'version' => '1.5.1',
    'state' => 'stable',
    'author' => 'Jared White, J. Max Wilson, Franz Holzinger',
    'author_email' => 'franz@ttproducts.de',
    'author_company' => 'jambage.com',
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.6.99',
            'typo3' => '12.4.0-14.3.99',
            'div2007' => '2.2.0-2.99.99',
        ],
        'suggests' => [
        ],
    ],
];
