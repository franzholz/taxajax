<?php

defined('TYPO3') || die('Access denied.');

if (!defined('TAXAJAX_EXT')) {
    define('TAXAJAX_EXT', 'taxajax');
}


/**
 * Define XAJAX_DEFAULT_CHAR_ENCODING that is used by both
 * the tx_xajax and tx_taxajax_response classes
 */
if (!defined('XAJAX_DEFAULT_CHAR_ENCODING')) {
    define('XAJAX_DEFAULT_CHAR_ENCODING', 'utf-8');
}


/**
 * Communication Method Defines
 */
if (!defined('XAJAX_GET')) {
    define('XAJAX_GET', 0);
}

if (!defined('XAJAX_POST')) {
    define('XAJAX_POST', 1);
}
