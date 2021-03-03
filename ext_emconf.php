<?php

########################################################################
# Extension Manager/Repository config file for ext "taxajax".
#
# Auto generated 15-09-2012 12:30
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 adapted xaJax',
    'description' => 'Enhancement to the xajax extension with TYPO3 specific code.',
    'category' => 'misc',
    'version' => '0.5.1',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Jared White, J. Max Wilson, Franz Holzinger',
    'author_email' => 'franz@ttproducts.de',
    'author_company' => 'jambage.com',
    'constraints' => [
        'depends' => [
            'php' => '5.6.0-7.4.99',
            'typo3' => '8.7.0-10.4.99',
        ],
        'suggests' => [
        ],
    ],
];

