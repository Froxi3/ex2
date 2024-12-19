<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
	die();
}

use \Bitrix\Main\Localization\Loc;

$arComponentParameters = [
	'PARAMETERS' => [
		'ID_IBLOCK_NEWS' => [
			'NAME' => Loc::getMessage('ID_IBLOCK_NEWS'),
			'TYPE' => 'STRING',
		],
		'IBLOCK_PROPERTY_AUTHOR' => [
			'NAME' => Loc::getMessage('IBLOCK_PROPERTY_AUTHOR'),
			'TYPE' => 'STRING',
		],
		'USER_PROPERTY_AUTHOR_TYPE' => [
			'NAME' => Loc::getMessage('USER_PROPERTY_AUTHOR_TYPE'),
			'TYPE' => 'STRING',
		],
		'CACHE_TIME'  =>  ['DEFAULT' => 36000000],	
	],
];
