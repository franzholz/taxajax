<?php

$emClass = '\\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility';

if (
	class_exists($emClass) &&
	method_exists($emClass, 'extPath')
) {
	// nothing
} else {
	$emClass = 't3lib_extMgm';
}

$key = 'taxajax';

$extensionPath = call_user_func($emClass . '::extPath', $key, $script);

return array(
	'tx_taxajax' => $extensionPath . 'class.tx_taxajax.php',
	'tx_taxajax_response' => $extensionPath . 'class.tx_taxajax_response.php',
	'xajaxCompress' => $extensionPath . 'xajaxCompress.php',
);

