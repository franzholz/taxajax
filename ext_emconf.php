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

$EM_CONF[$_EXTKEY] = array(
	'title' => 'TYPO3 adapted xaJax',
	'description' => 'Enhancement to the xajax extension with TYPO3 specific code.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '0.4.1',
	'dependencies' => '',
	'conflicts' => 'xajax',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Jared White, J. Max Wilson, Franz Holzinger',
	'author_email' => 'franz@ttproducts.de',
	'author_company' => 'ECT (Extension Coordination Team)',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.3-7.99.99',
			'typo3' => '4.5.0-8.99.99',
		),
		'conflicts' => array(
			'xajax' => '',
		),
		'suggests' => array(
		),
	),
);

