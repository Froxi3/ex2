<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
	'NAME' => Loc::getMessage('SIMPLE_COMP_TITLE'),
	'DESCRIPTION' => Loc::getMessage('SIMPLE_COMP_DESCRIPTION'),
	'PATH' => [
		'ID' => 'exam2',
		'NAME' => Loc::getMessage('SIMPLE_COMP_SECTION')
	],
];
