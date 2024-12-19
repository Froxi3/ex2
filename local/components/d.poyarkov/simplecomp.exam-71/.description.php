<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
	'NAME' => Loc::getMessage('SIMPLE_COMP_TITLE_71'),
	'DESCRIPTION' => Loc::getMessage('SIMPLE_COMP_DESCRIPTION_71'),
	'PATH' => [
		'ID' => 'exam2',
		'NAME' => Loc::getMessage('SIMPLE_COMP_SECTION_71')
	],
];
