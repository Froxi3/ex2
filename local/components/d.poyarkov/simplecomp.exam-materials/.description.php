<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	die();
}

use \Bitrix\Main\Localization\Loc;

$arComponentDescription = [
	'NAME' => Loc::getMessage('SIMPLECOMP_EXAM2_NAME'),
	'DESCRIPTION' => Loc::getMessage('SIMPLECOMP_EXAM2_DESC'),
	'PATH' => [
		'ID' => 'exam2',
		'NAME' => Loc::getMessage('SIMPLECOMP_EXAM2_SECTION'),
	],
];
